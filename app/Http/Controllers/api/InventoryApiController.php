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
     *   q      - search keyword
     *   mode   - 'sewa' | 'jual' (default: 'sewa')
     *   kondisi- 'baru' | 'bekas' (hanya untuk mode jual, default: 'baru')
     */
    public function index(Request $request)
    {
        $q       = trim($request->input('q', ''));
        $mode    = $request->input('mode', 'sewa');
        $kondisi = $request->input('kondisi', 'baru');

        $query = Inventory::query()
            ->when($q, fn($query) =>
                // FIXED: nama kolom yang benar adalah 'nama_produk'
                $query->where('nama_produk', 'like', "%{$q}%")
                      ->orWhere('kategori',  'like', "%{$q}%")
            );

        // Filter stok sesuai mode
        if ($mode === 'jual') {
            // Untuk jual: filter berdasarkan kondisi (baru/bekas)
            $stokField = $kondisi === 'bekas' ? 'stok_bekas' : 'stok_baru';
            $query->where($stokField, '>', 0);
        } else {
            // mode sewa — tampilkan semua yang stok tersedia > 0
            $query->where('stok_tersedia', '>', 0);
        }

        $items = $query->orderBy('nama_produk')->limit(50)->get();

        $results = $items->map(function (Inventory $item) use ($mode, $kondisi) {
            // Stok tersedia sesuai mode
            if ($mode === 'jual') {
                $stokTersedia = $kondisi === 'bekas'
                    ? ($item->stok_bekas ?? 0)
                    : ($item->stok_baru  ?? 0);
            } else {
                $stokTersedia = $item->stok_tersedia ?? 0;
            }

            // Harga — model hanya punya harga_beli_terakhir
            $harga = (int) ($item->harga_beli_terakhir ?? 0);

            // Stok status & label
            if ($stokTersedia <= 0)     $stokStatus = 'zero';
            elseif ($stokTersedia <= 3) $stokStatus = 'low';
            else                        $stokStatus = 'ok';

            $stokLabel = match (true) {
                $stokTersedia <= 0 => 'Stok Habis',
                $stokTersedia <= 3 => "Stok: {$stokTersedia} (menipis)",
                default            => "Stok: {$stokTersedia}",
            };

            return [
                'id'                  => $item->id,
                'text'                => $item->nama_produk,   // FIXED: nama_produk
                'kategori'            => $item->kategori  ?? '',
                'satuan'              => $item->satuan     ?? 'unit',
                'stok_baru'           => $item->stok_baru  ?? 0,
                'stok_bekas'          => $item->stok_bekas ?? 0,
                'stok_tersedia'       => $stokTersedia,
                'stok_status'         => $stokStatus,
                'stok_label'          => $stokLabel,
                'harga_beli_terakhir' => $harga,
            ];
        });

        return response()->json(['results' => $results]);
    }
}