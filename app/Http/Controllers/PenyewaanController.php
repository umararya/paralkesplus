<?php
// app/Http/Controllers/PenyewaanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penyewaan;

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

        return view('admin.penyewaan.index', compact('penyewaans', 'search', 'perPage'));
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

        // Simpan produk_alkes array → string CSV
        $validated['produk_alkes'] = implode(', ', $validated['produk_alkes']);

        // Upload foto KTP/SIM
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

        // Simpan produk_alkes array → string CSV
        $validated['produk_alkes'] = implode(', ', $validated['produk_alkes']);

        // Upload foto KTP/SIM jika ada file baru
        if ($request->hasFile('foto_ktp_sim')) {
            // Hapus file lama jika ada
            if ($penyewaan->foto_ktp_sim && \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
                \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
            }
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        } else {
            // Jangan overwrite nilai lama kalau tidak ada upload baru
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

        // Hapus file KTP/SIM dari storage
        if ($penyewaan->foto_ktp_sim && \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
            \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
        }

        $penyewaan->delete();

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil dihapus.');
    }
}