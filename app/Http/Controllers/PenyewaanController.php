<?php
// app/Http/Controllers/PenyewaanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penyewaan;
use App\Models\DetailPenyewaan;
use Carbon\Carbon;

class PenyewaanController extends Controller
{
    // =========================================================
    //  PRIVATE HELPER — sync status otomatis
    // =========================================================

    private function syncStatus(Penyewaan $item): void
    {
        if ($item->status === 'selesai') return;

        $sisaHari = $item->sisa_hari;

        if ($sisaHari <= 0) {
            $item->update(['status' => 'selesai']);
            return;
        }

        if ($sisaHari <= 3 && $item->status === 'berjalan') {
            $item->update(['status' => 'segera_konfirmasi']);
            return;
        }

        if ($sisaHari > 3 && $item->status === 'segera_konfirmasi') {
            $item->update(['status' => 'berjalan']);
        }
    }

    // =========================================================
    //  PRIVATE HELPER — simpan / sync detail items
    // =========================================================

    /**
     * Simpan array items ke tabel detail_penyewaans.
     * Kalau $deleteFirst = true, hapus dulu semua detail lama (untuk update).
     */
    private function syncDetails(Penyewaan $penyewaan, array $items, bool $deleteFirst = false): void
    {
        if ($deleteFirst) {
            $penyewaan->details()->delete();
        }

        $totalSewa = 0;

        foreach ($items as $item) {
            // skip baris kosong
            if (empty($item['nama_alat'])) continue;

            $qty         = max(1, (int) ($item['qty'] ?? 1));
            $harga       = max(0, (int) ($item['harga_satuan'] ?? 0));
            $diskon      = max(0, min(100, (int) ($item['diskon'] ?? 0)));
            $subtotal    = (int) round($qty * $harga * (1 - $diskon / 100));

            $penyewaan->details()->create([
                'nama_alat'    => $item['nama_alat'],
                'qty'          => $qty,
                'satuan'       => $item['satuan'] ?? 'pcs',
                'harga_satuan' => $harga,
                'diskon'       => $diskon,
                'subtotal'     => $subtotal,
            ]);

            $totalSewa += $subtotal;
        }

        // Update total_harga_sewa di header
        $penyewaan->update(['total_harga_sewa' => $totalSewa]);
    }

    // =========================================================
    //  INDEX
    // =========================================================

    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        $aktif = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])->get();
        foreach ($aktif as $item) {
            $this->syncStatus($item);
        }

        $penyewaans = Penyewaan::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_penyewa', 'like', "%{$search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$search}%")
                  ->orWhere('produk_alkes', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('pengiriman', 'like', "%{$search}%")
                  ->orWhere('alamat_penyewa', 'like', "%{$search}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('tgl_mulai', 'like', "%{$search}%")
                  ->orWhere('tgl_selesai', 'like', "%{$search}%")
                  ->orWhereRaw('CAST(durasi_hari AS CHAR) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('CAST(biaya_ongkir AS CHAR) LIKE ?', ["%{$search}%"]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penyewaan.index', compact('penyewaans', 'search', 'perPage'));
    }

    // =========================================================
    //  MONITORING & NOTIFIKASI (tidak ada perubahan logika)
    // =========================================================

    public function monitoring()
    {
        $aktif = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])->get();
        foreach ($aktif as $item) {
            $this->syncStatus($item);
        }

        $data = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])
            ->orderBy('tgl_selesai', 'asc')
            ->get()
            ->map(function ($item) {
                // Untuk label barang: kalau sudah punya detail, gabungkan nama_alat
                $barang = $item->has_detail
                    ? $item->details->pluck('nama_alat')->implode(', ')
                    : $item->produk_alkes;

                return [
                    'id'                => $item->id,
                    'nama'              => $item->nama_penyewa,
                    'nomor_hp'          => $item->nomor_telepon,
                    'barang'            => $barang,
                    'alamat'            => $item->alamat_penyewa,
                    'sisa_hari'         => $item->sisa_hari,
                    'tgl_selesai'       => $item->tgl_selesai
                                            ? $item->tgl_selesai->format('Y-m-d')
                                            : null,
                    'tgl_selesai_label' => $item->tgl_selesai
                                            ? $item->tgl_selesai->format('d M Y')
                                            : '-',
                    'status'            => $item->status,
                    'status_label'      => $item->status_label,
                    'status_class'      => $item->status_class,
                ];
            });

        return response()->json($data);
    }

    public function notifikasi()
    {
        $aktif = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])->get();
        foreach ($aktif as $item) {
            $this->syncStatus($item);
        }

        $data = Penyewaan::where('status', 'segera_konfirmasi')
            ->orderBy('tgl_selesai', 'asc')
            ->get()
            ->map(function ($item) {
                $sisaHari = $item->sisa_hari;

                if ($sisaHari <= 0) {
                    $sisaLabel = 'Lewat deadline!';
                } elseif ($sisaHari === 1) {
                    $sisaLabel = 'Besok deadline!';
                } elseif ($sisaHari === 2) {
                    $sisaLabel = '2 hari lagi';
                } else {
                    $sisaLabel = $sisaHari . ' hari lagi';
                }

                $barang = $item->has_detail
                    ? $item->details->pluck('nama_alat')->implode(', ')
                    : $item->produk_alkes;

                return [
                    'id'          => $item->id,
                    'nama'        => $item->nama_penyewa,
                    'barang'      => $barang,
                    'sisa_hari'   => $sisaHari,
                    'sisa_label'  => $sisaLabel,
                    'tgl_selesai' => $item->tgl_selesai
                                     ? $item->tgl_selesai->format('d M Y')
                                     : '-',
                ];
            });

        return response()->json([
            'count' => $data->count(),
            'items' => $data,
        ]);
    }

    // =========================================================
    //  SELESAIKAN & EXTEND (tidak ada perubahan)
    // =========================================================

    public function selesaikan(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);
        $action    = $request->input('action', 'selesai_sekarang');

        if ($action === 'selesai_sekarang') {
            $penyewaan->update([
                'status'      => 'selesai',
                'tgl_selesai' => Carbon::today()->format('Y-m-d'),
            ]);

            return response()->json([
                'success' => true,
                'action'  => 'selesai_sekarang',
                'message' => 'Penyewaan berhasil diselesaikan sekarang.',
            ]);
        }

        if ($action === 'sesuai_deadline') {
            return response()->json([
                'success' => true,
                'action'  => 'sesuai_deadline',
                'message' => 'Penyewaan akan otomatis selesai saat deadline tiba.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Action tidak dikenali.'], 422);
    }

    public function extend(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        $request->validate([
            'tgl_selesai_baru' => [
                'required',
                'date',
                'after:' . $penyewaan->tgl_selesai->format('Y-m-d'),
            ],
        ]);

        $tglBaru    = Carbon::parse($request->tgl_selesai_baru)->startOfDay();
        $tglMulai   = Carbon::parse($penyewaan->tgl_mulai)->startOfDay();
        $durasiHari = (int) $tglMulai->diffInDays($tglBaru);
        $sisaBaru   = (int) Carbon::today()->diffInDays($tglBaru, false);
        $newStatus  = $sisaBaru > 3 ? 'berjalan' : 'segera_konfirmasi';

        $penyewaan->update([
            'tgl_selesai' => $tglBaru->format('Y-m-d'),
            'durasi_hari' => $durasiHari,
            'status'      => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deadline berhasil di-extend ke ' . $tglBaru->format('d M Y') . '.',
        ]);
    }

    // =========================================================
    //  CREATE & STORE
    // =========================================================

    public function create()
    {
        return view('admin.penyewaan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penyewa'        => 'required|string|max:255',
            'nomor_telepon'       => 'required|string|max:20',
            'tgl_mulai'           => 'required|date',
            'tgl_selesai'         => 'required|date|after_or_equal:tgl_mulai',
            'durasi_hari'         => 'required|integer|min:1',
            'pengiriman'          => 'required|in:mandiri,Gosend / GrabExpress,Rental Mobil Paralkes',
            'biaya_ongkir'        => 'nullable|integer|min:0',
            'diskon_global'       => 'nullable|integer|min:0',
            'alamat_penyewa'      => 'required|string',
            'metode_pembayaran'   => 'required|in:Tunai / Cash,Transfer via Bank BCA',
            'bukti_pembayaran'    => 'nullable|string|max:500',
            'foto_ktp_sim'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status'              => 'required|in:berjalan',
            'keterangan'          => 'nullable|string',
            // Detail items
            'items'               => 'required|array|min:1',
            'items.*.nama_alat'   => 'required|string|max:255',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.satuan'      => 'required|string|max:50',
            'items.*.harga_satuan'=> 'required|integer|min:0',
            'items.*.diskon'      => 'nullable|integer|min:0|max:100',
        ]);

        if ($request->hasFile('foto_ktp_sim')) {
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        }

        $validated['biaya_ongkir']  = $validated['biaya_ongkir'] ?? 0;
        $validated['diskon_global'] = $validated['diskon_global'] ?? 0;
        $validated['total_harga_sewa'] = 0; // akan diupdate oleh syncDetails()

        // Buat header penyewaan (tanpa produk_alkes — sudah pakai sistem baru)
        $penyewaan = Penyewaan::create(collect($validated)->except('items')->toArray());

        // Simpan detail items
        $this->syncDetails($penyewaan, $validated['items']);

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil ditambahkan.');
    }

    // =========================================================
    //  SHOW
    // =========================================================

    public function show(string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);
        return view('admin.penyewaan.show', compact('penyewaan'));
    }

    // =========================================================
    //  EDIT & UPDATE
    // =========================================================

    public function edit(string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);
        return view('admin.penyewaan.edit', compact('penyewaan'));
    }

    public function update(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        $validated = $request->validate([
            'nama_penyewa'        => 'required|string|max:255',
            'nomor_telepon'       => 'required|string|max:20',
            'tgl_mulai'           => 'required|date',
            'tgl_selesai'         => 'required|date|after_or_equal:tgl_mulai',
            'durasi_hari'         => 'required|integer|min:1',
            'pengiriman'          => 'required|in:mandiri,Gosend / GrabExpress,Rental Mobil Paralkes',
            'biaya_ongkir'        => 'nullable|integer|min:0',
            'diskon_global'       => 'nullable|integer|min:0',
            'alamat_penyewa'      => 'required|string',
            'metode_pembayaran'   => 'required|in:Tunai / Cash,Transfer via Bank BCA',
            'bukti_pembayaran'    => 'nullable|string|max:500',
            'foto_ktp_sim'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status'              => 'required|in:berjalan,segera_konfirmasi,selesai',
            'keterangan'          => 'nullable|string',
            // Detail items
            'items'               => 'required|array|min:1',
            'items.*.nama_alat'   => 'required|string|max:255',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.satuan'      => 'required|string|max:50',
            'items.*.harga_satuan'=> 'required|integer|min:0',
            'items.*.diskon'      => 'nullable|integer|min:0|max:100',
        ]);

        if ($request->hasFile('foto_ktp_sim')) {
            if ($penyewaan->foto_ktp_sim && \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
                \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
            }
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        } else {
            unset($validated['foto_ktp_sim']);
        }

        $validated['biaya_ongkir']  = $validated['biaya_ongkir'] ?? 0;
        $validated['diskon_global'] = $validated['diskon_global'] ?? 0;

        // Update header
        $penyewaan->update(collect($validated)->except('items')->toArray());

        // Sync detail items (hapus lama, insert baru)
        $this->syncDetails($penyewaan, $validated['items'], deleteFirst: true);

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil diperbarui.');
    }

    // =========================================================
    //  DESTROY
    // =========================================================

    public function destroy(string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        if ($penyewaan->foto_ktp_sim && \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
            \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
        }

        // detail_penyewaans otomatis terhapus karena onDelete('cascade')
        $penyewaan->delete();

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil dihapus.');
    }

    // =========================================================
    //  CETAK INVOICE
    // =========================================================

    public function invoice(string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);
        return view('admin.penyewaan.cetak.invoice', compact('penyewaan'));
    }

    // =========================================================
    //  CETAK PERJANJIAN
    // =========================================================

    public function perjanjian(string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);
        return view('admin.penyewaan.cetak.perjanjian', compact('penyewaan'));
    }
}