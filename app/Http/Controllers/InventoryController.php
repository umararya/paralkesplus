<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search');
        $perPage = in_array($request->get('per_page'), [5, 10, 25, 50])
                   ? (int) $request->get('per_page')
                   : 10;

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

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk'         => 'required|string|max:150',
            'kategori'            => 'nullable|string|max:100',
            'satuan'              => 'required|in:unit,pcs,box,set,lusin',
            'stok_baru'           => 'required|integer|min:0',
            'stok_bekas'          => 'required|integer|min:0',
            'harga_beli_terakhir' => 'nullable|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {

            $stokBaru  = (int) $validated['stok_baru'];
            $stokBekas = (int) $validated['stok_bekas'];
            $totalQty  = $stokBaru + $stokBekas;

            // Cek apakah produk sudah ada (case-insensitive)
            $existing = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                strtolower($validated['nama_produk'])
            ])->first();

            if ($existing) {
                // Produk sudah ada → tambah stok saja
                $existing->stok_baru     += $stokBaru;
                $existing->stok_bekas    += $stokBekas;
                $existing->stok_tersedia += $totalQty;

                if (!empty($validated['harga_beli_terakhir'])) {
                    $existing->harga_beli_terakhir = $validated['harga_beli_terakhir'];
                }

                $existing->save();
                $inventory = $existing;

            } else {
                // Produk baru → buat entry baru
                $inventory = Inventory::create([
                    'nama_produk'         => $validated['nama_produk'],
                    'kategori'            => $validated['kategori'] ?? null,
                    'satuan'              => $validated['satuan'],
                    'stok_tersedia'       => $totalQty,
                    'stok_disewa'         => 0,
                    'stok_baru'           => $stokBaru,
                    'stok_bekas'          => $stokBekas,
                    'harga_beli_terakhir' => $validated['harga_beli_terakhir'] ?? null,
                    'keterangan'          => $validated['keterangan'] ?? null,
                ]);
            }

            // Catat inventory log
            // reference_id diisi 0 untuk input manual
            // (kolom NOT NULL di DB, tidak ada foreign key ke transaksi manapun)
            if ($totalQty > 0) {
                // Log per kondisi agar lebih detail
                if ($stokBaru > 0) {
                    InventoryLog::create([
                        'inventory_id'   => $inventory->id,
                        'reference_type' => 'manual',
                        'reference_id'   => 0,
                        'qty_change'     => $stokBaru,
                        'kondisi'        => 'baru',
                        'keterangan'     => 'Input manual: ' . $validated['nama_produk'],
                    ]);
                }

                if ($stokBekas > 0) {
                    InventoryLog::create([
                        'inventory_id'   => $inventory->id,
                        'reference_type' => 'manual',
                        'reference_id'   => 0,
                        'qty_change'     => $stokBekas,
                        'kondisi'        => 'bekas',
                        'keterangan'     => 'Input manual: ' . $validated['nama_produk'],
                    ]);
                }
            }

            ActivityLog::record(
                module:   'Inventory',
                action:   $existing ?? false ? 'update' : 'create',
                subject:  $validated['nama_produk'],
                newValue: [
                    'produk'     => $validated['nama_produk'],
                    'stok_baru'  => $stokBaru,
                    'stok_bekas' => $stokBekas,
                    'total_stok' => $totalQty,
                    'aksi'       => isset($existing) ? 'Tambah stok ke produk yang sudah ada' : 'Buat produk baru',
                ],
                pageUrl: 'inventory'
            );
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Data inventory berhasil ditambahkan.');
    }

    public function show(Inventory $inventory)
    {
        $logs = $inventory->logs()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.show', compact('inventory', 'logs'));
    }

    public function edit(Inventory $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'nama_produk'         => 'required|string|max:150',
            'kategori'            => 'nullable|string|max:100',
            'satuan'              => 'required|in:unit,pcs,box,set,lusin',
            'stok_baru'           => 'required|integer|min:0',
            'stok_bekas'          => 'required|integer|min:0',
            'stok_disewa'         => 'required|integer|min:0',
            'harga_beli_terakhir' => 'nullable|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        $oldData = [
            'produk'     => $inventory->nama_produk,
            'stok_baru'  => $inventory->stok_baru,
            'stok_bekas' => $inventory->stok_bekas,
        ];

        $validated['stok_tersedia'] = (int) $validated['stok_baru']
                                    + (int) $validated['stok_bekas'];

        $inventory->update($validated);

        ActivityLog::record(
            module:   'Inventory',
            action:   'update',
            subject:  $inventory->nama_produk,
            oldValue: $oldData,
            newValue: [
                'produk'     => $inventory->nama_produk,
                'stok_baru'  => $inventory->stok_baru,
                'stok_bekas' => $inventory->stok_bekas,
            ],
            pageUrl: 'inventory/' . $inventory->id . '/edit'
        );

        return redirect()->route('inventory.index')
            ->with('success', 'Data inventory berhasil diperbarui.');
    }

    public function destroy(Inventory $inventory)
    {
        ActivityLog::record(
            module:   'Inventory',
            action:   'delete',
            subject:  $inventory->nama_produk,
            oldValue: [
                'produk'     => $inventory->nama_produk,
                'stok_baru'  => $inventory->stok_baru,
                'stok_bekas' => $inventory->stok_bekas,
            ],
            pageUrl: 'inventory'
        );

        // Hapus log terkait dulu sebelum hapus inventory
        InventoryLog::where('inventory_id', $inventory->id)->delete();

        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Data inventory berhasil dihapus.');
    }
}