<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $inventories = Inventory::when($search, function ($query) use ($search) {
                $query->where('nama_produk', 'like', "%{$search}%")
                      ->orWhere('kategori', 'like', "%{$search}%");
            })
            ->orderBy('nama_produk')
            ->paginate($perPage)
            ->withQueryString();

        $summary = [
            'total_item'     => Inventory::count(),
            'total_tersedia' => Inventory::sum('stok_tersedia'),
            'total_disewa'   => Inventory::sum('stok_disewa'),
            'total_bekas'    => Inventory::sum('stok_bekas'),
        ];

        return view('admin.inventory.index', compact('inventories', 'summary', 'search', 'perPage'));
    }

    public function show(Inventory $inventory)
    {
        $logs = $inventory->logs()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.show', compact('inventory', 'logs'));
    }
}