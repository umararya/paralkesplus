<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// Uncomment setelah Model dibuat:
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

        // ── Placeholder sementara ──
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
        // TODO: uncomment setelah Model Inventory dibuat
        // $validated = $request->validate([...]);
        // $inventory = Inventory::create($validated);
        //
        // ActivityLog::record(
        //     module:   'Inventory',
        //     action:   'create',
        //     subject:  $inventory->nama_barang,
        //     newValue: [
        //         'barang'   => $inventory->nama_barang,
        //         'stok'     => $inventory->stok,
        //         'kondisi'  => $inventory->kondisi,
        //     ],
        //     pageUrl: 'inventory'
        // );

        return redirect()->route('inventory.index')
            ->with('success', 'Barang berhasil ditambahkan.');
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
        // TODO: uncomment setelah Model Inventory dibuat
        // $inventory = Inventory::findOrFail($id);
        // $oldData = ['stok' => $inventory->stok, 'kondisi' => $inventory->kondisi];
        // $inventory->update($validated);
        //
        // ActivityLog::record(
        //     module:   'Inventory',
        //     action:   'update',
        //     subject:  $inventory->nama_barang,
        //     oldValue: $oldData,
        //     newValue: ['stok' => $inventory->stok, 'kondisi' => $inventory->kondisi],
        //     pageUrl:  'inventory/' . $id . '/edit'
        // );

        return redirect()->route('inventory.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // TODO: uncomment setelah Model Inventory dibuat
        // $inventory = Inventory::findOrFail($id);
        //
        // ActivityLog::record(
        //     module:   'Inventory',
        //     action:   'delete',
        //     subject:  $inventory->nama_barang,
        //     oldValue: ['barang' => $inventory->nama_barang, 'stok' => $inventory->stok],
        //     pageUrl:  'inventory'
        // );
        //
        // $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}