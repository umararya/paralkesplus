<?php
// app/Http/Controllers/PembelianController.php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;

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
                  ->orWhereRaw("DATE_FORMAT(tanggal_pembelian, '%d/%m/%Y') like ?", ["%{$search}%"]);
            })
            ->orderBy('tanggal_pembelian', 'desc')
            ->paginate($perPage)
            ->withQueryString(); // pertahankan ?search= & ?per_page= di link pagination

        // Hitung total keseluruhan dari query yang sudah difilter (bukan hanya halaman ini)
        $totalKeseluruhan = Pembelian::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereRaw("DATE_FORMAT(tanggal_pembelian, '%d/%m/%Y') like ?", ["%{$search}%"]);
            })
            ->get()
            ->sum(fn($p) => $p->jumlah * $p->harga_satuan);

        return view('admin.pembelian.index', compact('pembelians', 'search', 'perPage', 'totalKeseluruhan'));
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
            'harga_satuan'      => 'required|integer|min:0',
            'keterangan'        => 'nullable|string',
        ]);

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
            'harga_satuan'      => 'required|integer|min:0',
            'keterangan'        => 'nullable|string',
        ]);

        $pembelian->update($validated);

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->delete();

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil dihapus.');
    }
}