<?php
// app/Http/Controllers/Api/InventoryApiController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    /**
     * GET /api/inventory
     * Query params:
     *   q       - search keyword
     *   mode    - 'sewa' | 'jual' (default: 'sewa')
     *   kondisi - 'baru' | 'bekas' (hanya untuk mode jual, default: 'baru')
     */
    public function index(Request $request)
    {
        $q       = trim($request->input('q', ''));
        $mode    = $request->input('mode', 'sewa');
        $kondisi = $request->input('kondisi', 'baru');

        $query = Inventory::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('kategori', 'like', "%{$q}%");
                });
            });

        if ($mode === 'jual') {
            $stokField = $kondisi === 'bekas' ? 'stok_bekas' : 'stok_baru';
            $query->where($stokField, '>', 0);
        } else {
            $query->where(function ($sub) {
                $sub->where('stok_baru', '>', 0)
                    ->orWhere('stok_bekas', '>', 0)
                    ->orWhere('stok_tersedia', '>', 0);
            });
        }

        $items = $query->orderBy('nama_produk')->limit(50)->get();

        $results = $items->map(function (Inventory $item) use ($mode, $kondisi) {
            $stokBaru  = (int) ($item->stok_baru ?? 0);
            $stokBekas = (int) ($item->stok_bekas ?? 0);
            $stokTotal = (int) ($item->stok_tersedia ?? ($stokBaru + $stokBekas));
            $harga     = (int) ($item->harga_beli_terakhir ?? 0);

            if ($mode === 'jual') {
                $stokTersedia = $kondisi === 'bekas' ? $stokBekas : $stokBaru;

                $stokLabel = match (true) {
                    $stokTersedia <= 0 => 'Stok Habis',
                    $stokTersedia <= 3 => 'Stok: ' . $stokTersedia . ' (menipis)',
                    default            => 'Stok: ' . $stokTersedia,
                };
            } else {
                $stokTersedia = max(0, $stokTotal);

                if ($stokBaru > 0 && $stokBekas > 0) {
                    $stokLabel = "Sewa tersedia — Baru: {$stokBaru}, Bekas: {$stokBekas}, Total: {$stokTersedia}";
                } elseif ($stokBaru > 0) {
                    $stokLabel = "Sewa tersedia — Baru: {$stokBaru}, Bekas: {$stokBekas}";
                } elseif ($stokBekas > 0) {
                    $stokLabel = "Sewa tersedia — Bekas: {$stokBekas}, Baru: {$stokBaru}";
                } else {
                    $stokLabel = "Sewa tersedia — Total: {$stokTersedia}";
                }
            }

            if ($stokTersedia <= 0) {
                $stokStatus = 'zero';
            } elseif ($stokTersedia <= 3) {
                $stokStatus = 'low';
            } else {
                $stokStatus = 'ok';
            }

            return [
                'id'                  => $item->id,
                'text'                => $item->nama_produk,
                'kategori'            => $item->kategori ?? '',
                'satuan'              => $item->satuan ?? 'unit',
                'stok_baru'           => $stokBaru,
                'stok_bekas'          => $stokBekas,
                'stok_tersedia'       => $stokTersedia,
                'stok_total_display'  => $stokTotal,
                'stok_status'         => $stokStatus,
                'stok_label'          => $stokLabel,
                'harga_beli_terakhir' => $harga,
            ];
        });

        return response()->json([
            'results' => $results,
        ]);
    }
}