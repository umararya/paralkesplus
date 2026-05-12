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
                  ->orWhere('status', 'like', "%{$search}%");
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
            'nama_penyewa'                    => 'required|string|max:255',
            'nomor_telepon'                   => 'required|string|max:20',
            'produk_alkes'                    => 'required|string|max:255',
            'durasi_hari'                     => 'required|integer|min:1',
            'pengiriman_ditanggung_pelanggan' => 'required|in:1,0',
            'biaya_ongkir'                    => 'nullable|integer|min:0',
            'alamat_penyewa'                  => 'required|string',
            'metode_pembayaran'               => 'required|string|max:100',
            'bukti_pembayaran'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktp_sim'                    => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'status'                          => 'required|in:berjalan',
            'keterangan'                      => 'nullable|string',
        ]);

        // Upload bukti pembayaran
        if ($request->hasFile('bukti_pembayaran')) {
            $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')
                ->store('penyewaan/bukti', 'public');
        }

        // Upload foto KTP/SIM
        if ($request->hasFile('foto_ktp_sim')) {
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        }

        $validated['pengiriman_ditanggung_pelanggan'] = (bool) $validated['pengiriman_ditanggung_pelanggan'];
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
            'nama_penyewa'                    => 'required|string|max:255',
            'nomor_telepon'                   => 'required|string|max:20',
            'produk_alkes'                    => 'required|string|max:255',
            'durasi_hari'                     => 'required|integer|min:1',
            'pengiriman_ditanggung_pelanggan' => 'required|in:1,0',
            'biaya_ongkir'                    => 'nullable|integer|min:0',
            'alamat_penyewa'                  => 'required|string',
            'metode_pembayaran'               => 'required|string|max:100',
            'bukti_pembayaran'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktp_sim'                    => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'status'                          => 'required|in:berjalan,segera_konfirmasi,selesai',
            'keterangan'                      => 'nullable|string',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')
                ->store('penyewaan/bukti', 'public');
        }

        if ($request->hasFile('foto_ktp_sim')) {
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        }

        $validated['pengiriman_ditanggung_pelanggan'] = (bool) $validated['pengiriman_ditanggung_pelanggan'];
        $validated['biaya_ongkir'] = $validated['biaya_ongkir'] ?? 0;

        $penyewaan->update($validated);

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);
        $penyewaan->delete();

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil dihapus.');
    }
}