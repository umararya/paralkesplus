<?php
// app/Http/Controllers/PenjualanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// Uncomment baris ini setelah Model dibuat:
// use App\Models\Penjualan;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        // ── Gunakan ini setelah Model Penjualan dibuat ──
        // $penjualans = Penjualan::query()
        //     ->when($search, function ($q) use ($search) {
        //         $q->where('nama_pembeli', 'like', "%{$search}%")
        //           ->orWhere('nama_produk', 'like', "%{$search}%")
        //           ->orWhere('status', 'like', "%{$search}%");
        //     })
        //     ->orderBy('tanggal_penjualan', 'desc')
        //     ->paginate($perPage)
        //     ->withQueryString();

        // ── Placeholder sementara (hapus setelah Model dibuat) ──
        $penjualans = new LengthAwarePaginator(
            new Collection([]),
            0, $perPage, 1,
            ['path' => route('penjualan.index')]
        );

        return view('admin.penjualan.index', compact('penjualans', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.penjualan.create');
    }

    public function store(Request $request)
    {
        // TODO: Implementasi simpan data penjualan
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return view('admin.penjualan.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('admin.penjualan.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        // TODO: Implementasi update data penjualan
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // TODO: Implementasi hapus data penjualan
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil dihapus.');
    }
}