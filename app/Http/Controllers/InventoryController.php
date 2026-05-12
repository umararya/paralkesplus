<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// Uncomment baris ini setelah Model dibuat:
// use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        // ── Gunakan ini setelah Model Inventory dibuat ──
        // $inventories = Inventory::query()
        //     ->when($search, function ($q) use ($search) {
        //         $q->where('nama_barang', 'like', "%{$search}%")
        //           ->orWhere('kode_barang', 'like', "%{$search}%")
        //           ->orWhere('kategori', 'like', "%{$search}%")
        //           ->orWhere('kondisi', 'like', "%{$search}%");
        //     })
        //     ->orderBy('created_at', 'desc')
        //     ->paginate($perPage)
        //     ->withQueryString();

        // ── Placeholder sementara (hapus setelah Model dibuat) ──
        $inventories = new LengthAwarePaginator(
            new Collection([]),
            0, $perPage, 1,
            ['path' => route('inventory.index')]
        );

        return view('admin.inventory.index', compact('inventories', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        // TODO: Implementasi simpan data inventory
        return redirect()->route('inventory.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return view('admin.inventory.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('admin.inventory.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        // TODO: Implementasi update data inventory
        return redirect()->route('inventory.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // TODO: Implementasi hapus data inventory
        return redirect()->route('inventory.index')->with('success', 'Barang berhasil dihapus.');
    }
}