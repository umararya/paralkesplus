<?php
// app/Http/Controllers/PembelianController.php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        $pembelians = Pembelian::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('tanggal_pembelian', 'like', "%{$search}%");
            })
            ->orderBy('tanggal_pembelian', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $totalKeseluruhan = Pembelian::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('tanggal_pembelian', 'like', "%{$search}%");
            })
            ->sum('total');

        return view('admin.pembelian.index', compact(
            'pembelians', 'search', 'perPage', 'totalKeseluruhan'
        ));
    }

    public function create()
    {
        return view('admin.pembelian.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('bukti_transaksi')) {
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')
                ->store('pembelian/bukti', 'public');
        }

        Pembelian::create($validated);

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        return view('admin.pembelian.show', compact('pembelian'));
    }

    public function edit(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        return view('admin.pembelian.edit', compact('pembelian'));
    }

    public function update(Request $request, string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // Ada upload gambar baru → hapus yang lama, simpan yang baru
        if ($request->hasFile('bukti_transaksi')) {
            if ($pembelian->bukti_transaksi) {
                Storage::disk('public')->delete($pembelian->bukti_transaksi);
            }
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')
                ->store('pembelian/bukti', 'public');
        }
        // Centang hapus bukti tanpa upload baru → set null
        elseif ($request->has('hapus_bukti')) {
            if ($pembelian->bukti_transaksi) {
                Storage::disk('public')->delete($pembelian->bukti_transaksi);
            }
            $validated['bukti_transaksi'] = null;
        }
        // Tidak ada perubahan gambar → pertahankan yang lama
        else {
            unset($validated['bukti_transaksi']);
        }

        $pembelian->update($validated);

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->bukti_transaksi) {
            Storage::disk('public')->delete($pembelian->bukti_transaksi);
        }

        $pembelian->delete();

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil dihapus.');
    }
}