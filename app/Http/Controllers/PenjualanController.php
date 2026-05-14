<?php
// app/Http/Controllers/PenjualanController.php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        $penjualans = Penjualan::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('alamat_pelanggan', 'like', "%{$search}%")
                  ->orWhere('jenis_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            })
            ->orderBy('tanggal_penjualan', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penjualan.index', compact('penjualans', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.penjualan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_penjualan' => 'required|date',
            'nama_barang'       => 'required|string|max:255',
            'qty'               => 'required|integer|min:1',
            'alamat_pelanggan'  => 'required|string|max:500',
            'jenis_pembayaran'  => 'required|in:tunai,transfer,qris,kredit',
            'harga'             => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string|max:1000',
        ]);

        Penjualan::create($validated);

        return redirect()->route('penjualan.index')
                         ->with('success', 'Data penjualan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('admin.penjualan.show', compact('penjualan'));
    }

    public function edit(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('admin.penjualan.edit', compact('penjualan'));
    }

    public function update(Request $request, string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        $validated = $request->validate([
            'tanggal_penjualan' => 'required|date',
            'nama_barang'       => 'required|string|max:255',
            'qty'               => 'required|integer|min:1',
            'alamat_pelanggan'  => 'required|string|max:500',
            'jenis_pembayaran'  => 'required|in:tunai,transfer,qris,kredit',
            'harga'             => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string|max:1000',
        ]);

        $penjualan->update($validated);

        return redirect()->route('penjualan.index')
                         ->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        return redirect()->route('penjualan.index')
                         ->with('success', 'Data penjualan berhasil dihapus.');
    }
}