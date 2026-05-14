<?php
// app/Http/Controllers/PenyewaanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penyewaan;
use Carbon\Carbon;

class PenyewaanController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        $penyewaans = Penyewaan::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_penyewa', 'like', "%{$search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$search}%")
                  ->orWhere('produk_alkes', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('pengiriman', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Auto-update status berdasarkan sisa hari
        foreach ($penyewaans as $item) {
            if (in_array($item->status, ['berjalan', 'segera_konfirmasi'])) {
                $sisaHari = $item->sisa_hari;
                if ($sisaHari <= 3 && $item->status === 'berjalan') {
                    $item->update(['status' => 'segera_konfirmasi']);
                } elseif ($sisaHari > 3 && $item->status === 'segera_konfirmasi') {
                    $item->update(['status' => 'berjalan']);
                }
            }
        }

        // Refresh setelah update
        $penyewaans = Penyewaan::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_penyewa', 'like', "%{$search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$search}%")
                  ->orWhere('produk_alkes', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('pengiriman', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penyewaan.index', compact('penyewaans', 'search', 'perPage'));
    }

    /**
     * Ambil data monitoring (berjalan & segera_konfirmasi) via AJAX
     */
    public function monitoring()
    {
        $data = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])
            ->orderBy('tgl_selesai', 'asc')
            ->get()
            ->map(function ($item) {
                // Recalculate dan update status
                $sisaHari = $item->sisa_hari;
                if ($sisaHari <= 3 && $item->status === 'berjalan') {
                    $item->update(['status' => 'segera_konfirmasi']);
                    $item->refresh();
                } elseif ($sisaHari > 3 && $item->status === 'segera_konfirmasi') {
                    $item->update(['status' => 'berjalan']);
                    $item->refresh();
                }

                return [
                    'id'            => $item->id,
                    'nama'          => $item->nama_penyewa,
                    'nomor_hp'      => $item->nomor_telepon,
                    'barang'        => $item->produk_alkes,
                    'alamat'        => $item->alamat_penyewa,
                    'sisa_hari'     => $item->sisa_hari,
                    'tgl_selesai'   => $item->tgl_selesai ? $item->tgl_selesai->format('Y-m-d') : null,
                    'tgl_selesai_label' => $item->tgl_selesai ? $item->tgl_selesai->format('d M Y') : '-',
                    'status'        => $item->status,
                    'status_label'  => $item->status_label,
                    'status_class'  => $item->status_class,
                ];
            });

        return response()->json($data);
    }

    /**
     * Selesaikan penyewaan (langsung / sesuai deadline)
     */
    public function selesaikan(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);
        $action    = $request->input('action', 'selesai_sekarang');

        if ($action === 'selesai_sekarang') {
            $penyewaan->update([
                'status'     => 'selesai',
                'tgl_selesai' => Carbon::today(),
            ]);
        } elseif ($action === 'sesuai_deadline') {
            $penyewaan->update(['status' => 'selesai']);
        }

        return response()->json(['success' => true, 'message' => 'Penyewaan berhasil diselesaikan.']);
    }

    /**
     * Extend deadline penyewaan
     */
    public function extend(Request $request, string $id)
    {
        $request->validate([
            'tgl_selesai_baru' => 'required|date|after:today',
        ]);

        $penyewaan = Penyewaan::findOrFail($id);
        $tglBaru   = Carbon::parse($request->tgl_selesai_baru);
        $tglLama   = Carbon::parse($penyewaan->tgl_selesai);

        // Hitung ulang durasi dari tgl_mulai ke tgl_selesai baru
        $tglMulai    = Carbon::parse($penyewaan->tgl_mulai);
        $durasiHari  = $tglMulai->diffInDays($tglBaru);

        $penyewaan->update([
            'tgl_selesai' => $tglBaru->format('Y-m-d'),
            'durasi_hari' => $durasiHari,
            'status'      => 'berjalan',
        ]);

        return response()->json(['success' => true, 'message' => 'Deadline berhasil di-extend.']);
    }

    public function create()
    {
        return view('admin.penyewaan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penyewa'      => 'required|string|max:255',
            'nomor_telepon'     => 'required|string|max:20',
            'produk_alkes'      => 'required|array|min:1',
            'produk_alkes.*'    => 'required|string|max:100',
            'tgl_mulai'         => 'required|date',
            'tgl_selesai'       => 'required|date|after_or_equal:tgl_mulai',
            'durasi_hari'       => 'required|integer|min:1',
            'pengiriman'        => 'required|in:mandiri,Gosend / GrabExpress,Rental Mobil Paralkes',
            'biaya_ongkir'      => 'nullable|integer|min:0',
            'alamat_penyewa'    => 'required|string',
            'metode_pembayaran' => 'required|in:Tunai / Cash,Transfer via Bank BCA',
            'bukti_pembayaran'  => 'nullable|string|max:500',
            'foto_ktp_sim'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status'            => 'required|in:berjalan',
            'keterangan'        => 'nullable|string',
        ]);

        $validated['produk_alkes'] = implode(', ', $validated['produk_alkes']);

        if ($request->hasFile('foto_ktp_sim')) {
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        } else {
            unset($validated['foto_ktp_sim']);
        }

        $validated['biaya_ongkir'] = $validated['biaya_ongkir'] ?? 0;

        Penyewaan::create($validated);

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);
        return view('admin.penyewaan.show', compact('penyewaan'));
    }

    public function edit(string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);
        return view('admin.penyewaan.edit', compact('penyewaan'));
    }

    public function update(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        $validated = $request->validate([
            'nama_penyewa'      => 'required|string|max:255',
            'nomor_telepon'     => 'required|string|max:20',
            'produk_alkes'      => 'required|array|min:1',
            'produk_alkes.*'    => 'required|string|max:100',
            'tgl_mulai'         => 'required|date',
            'tgl_selesai'       => 'required|date|after_or_equal:tgl_mulai',
            'durasi_hari'       => 'required|integer|min:1',
            'pengiriman'        => 'required|in:mandiri,Gosend / GrabExpress,Rental Mobil Paralkes',
            'biaya_ongkir'      => 'nullable|integer|min:0',
            'alamat_penyewa'    => 'required|string',
            'metode_pembayaran' => 'required|in:Tunai / Cash,Transfer via Bank BCA',
            'bukti_pembayaran'  => 'nullable|string|max:500',
            'foto_ktp_sim'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status'            => 'required|in:berjalan,segera_konfirmasi,selesai',
            'keterangan'        => 'nullable|string',
        ]);

        $validated['produk_alkes'] = implode(', ', $validated['produk_alkes']);

        if ($request->hasFile('foto_ktp_sim')) {
            if ($penyewaan->foto_ktp_sim && \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
                \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
            }
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        } else {
            unset($validated['foto_ktp_sim']);
        }

        $validated['biaya_ongkir'] = $validated['biaya_ongkir'] ?? 0;

        $penyewaan->update($validated);

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        if ($penyewaan->foto_ktp_sim && \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
            \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
        }

        $penyewaan->delete();

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil dihapus.');
    }
}