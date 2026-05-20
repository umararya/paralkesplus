<?php
// app/Http/Controllers/PembelianController.php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $filter  = $request->input('filter', 'semua');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        $query = Pembelian::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('tanggal_pembelian', 'like', "%{$search}%")
                  ->orWhereRaw('CAST(jumlah AS CHAR) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('CAST(harga_satuan AS CHAR) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('CAST(total AS CHAR) LIKE ?', ["%{$search}%"]);
            })
            ->when($filter !== 'semua', function ($q) use ($filter) {
                $q->where('status', $filter);
            });

        $pembelians       = (clone $query)->orderBy('tanggal_pembelian', 'desc')->paginate($perPage)->withQueryString();
        $totalKeseluruhan = (clone $query)->sum('total');

        $countSemua   = Pembelian::count();
        $countNormal  = Pembelian::where('status', 'normal')->count();
        $countBuyBack = Pembelian::where('status', 'buy_back')->count();

        return view('admin.pembelian.index', compact(
            'pembelians', 'search', 'perPage', 'totalKeseluruhan',
            'filter', 'countSemua', 'countNormal', 'countBuyBack'
        ));
    }

    public function create()
    {
        $inventoryItems = Inventory::orderBy('nama_produk')->get(['id', 'nama_produk']);

        $summary = [
            'total_item'     => Inventory::count(),
            'total_tersedia' => Inventory::sum('stok_tersedia'),
            'total_disewa'   => Inventory::sum('stok_disewa'),
            'total_bekas'    => Inventory::sum('stok_bekas'),
        ];

        return view('admin.pembelian.create', compact('inventoryItems', 'summary'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'kondisi_barang'    => 'required|in:baru,bekas',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $validated['status'] = 'normal';

        if ($request->hasFile('bukti_transaksi')) {
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')
                ->store('pembelian/bukti', 'public');
        }

        $namaBarang  = $validated['nama_barang'];
        $jumlah      = (int) $validated['jumlah'];
        $hargaSatuan = (float) $validated['harga_satuan'];
        $kondisi     = $validated['kondisi_barang'];

        DB::transaction(function () use ($validated, $namaBarang, $jumlah, $hargaSatuan, $kondisi) {

            $pembelian = Pembelian::create($validated);
            $pembelian->refresh();

            $inventory = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                strtolower($namaBarang)
            ])->first();

            if ($inventory) {
                $inventory->stok_tersedia      += $jumlah;
                $inventory->harga_beli_terakhir = $hargaSatuan;

                if ($kondisi === 'baru') {
                    $inventory->stok_baru += $jumlah;
                } else {
                    $inventory->stok_bekas += $jumlah;
                }

                $inventory->save();
            } else {
                $inventory = Inventory::create([
                    'nama_produk'         => $namaBarang,
                    'kategori'            => null,
                    'satuan'              => 'unit',
                    'stok_tersedia'       => $jumlah,
                    'stok_disewa'         => 0,
                    'stok_baru'           => $kondisi === 'baru'  ? $jumlah : 0,
                    'stok_bekas'          => $kondisi === 'bekas' ? $jumlah : 0,
                    'harga_beli_terakhir' => $hargaSatuan,
                ]);
            }

            InventoryLog::create([
                'inventory_id'   => $inventory->id,
                'reference_type' => 'purchase',
                'reference_id'   => $pembelian->id,
                'qty_change'     => $jumlah,
                'kondisi'        => $kondisi,
                'keterangan'     => 'Pembelian: ' . $namaBarang,
            ]);

            ActivityLog::record(
                module:   'Pembelian',
                action:   'create',
                subject:  $namaBarang,
                newValue: [
                    'barang'  => $namaBarang,
                    'jumlah'  => $jumlah,
                    'kondisi' => $kondisi,
                    'total'   => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
                    'tanggal' => $pembelian->tanggal_pembelian,
                ],
                pageUrl: 'pembelian'
            );
        });

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil ditambahkan dan stok inventory diperbarui.');
    }

    public function show(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $inventoryItem = Inventory::whereRaw('LOWER(nama_produk) = ?', [
            strtolower($pembelian->nama_barang)
        ])->first();

        return view('admin.pembelian.show', compact('pembelian', 'inventoryItem'));
    }

    public function edit(string $id)
    {
        $pembelian      = Pembelian::findOrFail($id);
        $inventoryItems = Inventory::orderBy('nama_produk')->get(['id', 'nama_produk']);

        return view('admin.pembelian.edit', compact('pembelian', 'inventoryItems'));
    }

    public function update(Request $request, string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'kondisi_barang'    => 'required|in:baru,bekas',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // ── Snapshot data LAMA ──
        $oldNama    = $pembelian->nama_barang;
        $oldJumlah  = (int) $pembelian->jumlah;
        $oldKondisi = $pembelian->kondisi_barang;

        $oldData = [
            'barang'  => $oldNama,
            'jumlah'  => $oldJumlah,
            'kondisi' => $oldKondisi,
            'total'   => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
        ];

        // ── Handle bukti transaksi ──
        if ($request->hasFile('bukti_transaksi')) {
            if ($pembelian->bukti_transaksi) {
                Storage::disk('public')->delete($pembelian->bukti_transaksi);
            }
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')
                ->store('pembelian/bukti', 'public');
        } elseif ($request->has('hapus_bukti')) {
            if ($pembelian->bukti_transaksi) {
                Storage::disk('public')->delete($pembelian->bukti_transaksi);
            }
            $validated['bukti_transaksi'] = null;
        } else {
            unset($validated['bukti_transaksi']);
        }

        $newNama    = $validated['nama_barang'];
        $newJumlah  = (int) $validated['jumlah'];
        $newKondisi = $validated['kondisi_barang'];
        $newHarga   = (float) $validated['harga_satuan'];

        $namaBeruban = strtolower($oldNama) !== strtolower($newNama);

        DB::transaction(function () use (
            $pembelian, $validated,
            $oldNama, $oldJumlah, $oldKondisi,
            $newNama, $newJumlah, $newKondisi, $newHarga,
            $namaBeruban, $oldData
        ) {
            // 1. Update data pembelian
            $pembelian->update($validated);
            $pembelian->refresh();

            // ══════════════════════════════════════
            //  KASUS A: Nama barang TIDAK berubah
            //  → update langsung entry inventory yg sama
            // ══════════════════════════════════════
            if (!$namaBeruban) {
                $inventory = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                    strtolower($newNama)
                ])->first();

                if ($inventory) {
                    // Hitung ulang stok: kurangi lama, tambah baru
                    $diffJumlah = $newJumlah - $oldJumlah;

                    $inventory->stok_tersedia      = max(0, $inventory->stok_tersedia + $diffJumlah);
                    $inventory->harga_beli_terakhir = $newHarga;

                    // Kondisi sama → diff langsung
                    if ($oldKondisi === $newKondisi) {
                        if ($newKondisi === 'baru') {
                            $inventory->stok_baru  = max(0, $inventory->stok_baru + $diffJumlah);
                        } else {
                            $inventory->stok_bekas = max(0, $inventory->stok_bekas + $diffJumlah);
                        }
                    } else {
                        // Kondisi berubah (baru→bekas atau bekas→baru)
                        // Kurangi dari kondisi lama, tambah ke kondisi baru
                        if ($oldKondisi === 'baru') {
                            $inventory->stok_baru  = max(0, $inventory->stok_baru - $oldJumlah);
                            $inventory->stok_bekas = max(0, $inventory->stok_bekas + $newJumlah);
                        } else {
                            $inventory->stok_bekas = max(0, $inventory->stok_bekas - $oldJumlah);
                            $inventory->stok_baru  = max(0, $inventory->stok_baru + $newJumlah);
                        }
                    }

                    $inventory->save();

                    InventoryLog::create([
                        'inventory_id'   => $inventory->id,
                        'reference_type' => 'purchase',
                        'reference_id'   => $pembelian->id,
                        'qty_change'     => $newJumlah,
                        'kondisi'        => $newKondisi,
                        'keterangan'     => 'Edit Pembelian: ' . $newNama,
                    ]);
                }

            // ══════════════════════════════════════
            //  KASUS B: Nama barang BERUBAH
            //  → hapus entry inventory lama
            //  → buat / update entry inventory baru
            // ══════════════════════════════════════
            } else {
                // Hapus (atau kurangi stok) entry inventory LAMA
                $invLama = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                    strtolower($oldNama)
                ])->first();

                if ($invLama) {
                    // Jika stok tersedia habis setelah dikurangi → hapus entry
                    $sisaStok = $invLama->stok_tersedia - $oldJumlah;

                    if ($sisaStok <= 0) {
                        // Hapus seluruh entry inventory lama
                        InventoryLog::where('inventory_id', $invLama->id)->delete();
                        $invLama->delete();
                    } else {
                        // Masih ada stok dari sumber lain → hanya kurangi
                        $invLama->stok_tersedia = $sisaStok;

                        if ($oldKondisi === 'baru') {
                            $invLama->stok_baru = max(0, $invLama->stok_baru - $oldJumlah);
                        } else {
                            $invLama->stok_bekas = max(0, $invLama->stok_bekas - $oldJumlah);
                        }

                        $invLama->save();
                    }
                }

                // Buat / update entry inventory BARU
                $invBaru = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                    strtolower($newNama)
                ])->first();

                if ($invBaru) {
                    $invBaru->stok_tersedia      += $newJumlah;
                    $invBaru->harga_beli_terakhir = $newHarga;

                    if ($newKondisi === 'baru') {
                        $invBaru->stok_baru += $newJumlah;
                    } else {
                        $invBaru->stok_bekas += $newJumlah;
                    }

                    $invBaru->save();
                } else {
                    $invBaru = Inventory::create([
                        'nama_produk'         => $newNama,
                        'kategori'            => null,
                        'satuan'              => 'unit',
                        'stok_tersedia'       => $newJumlah,
                        'stok_disewa'         => 0,
                        'stok_baru'           => $newKondisi === 'baru'  ? $newJumlah : 0,
                        'stok_bekas'          => $newKondisi === 'bekas' ? $newJumlah : 0,
                        'harga_beli_terakhir' => $newHarga,
                    ]);
                }

                InventoryLog::create([
                    'inventory_id'   => $invBaru->id,
                    'reference_type' => 'purchase',
                    'reference_id'   => $pembelian->id,
                    'qty_change'     => $newJumlah,
                    'kondisi'        => $newKondisi,
                    'keterangan'     => 'Edit Pembelian: ' . $newNama . ' (dari: ' . $oldNama . ')',
                ]);
            }

            // Activity log
            ActivityLog::record(
                module:   'Pembelian',
                action:   'update',
                subject:  $newNama,
                oldValue: $oldData,
                newValue: [
                    'barang'  => $newNama,
                    'jumlah'  => $newJumlah,
                    'kondisi' => $newKondisi,
                    'total'   => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
                ],
                pageUrl: 'pembelian/' . $pembelian->id . '/edit'
            );
        });

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian dan stok inventory berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        DB::transaction(function () use ($pembelian) {
            // Rollback stok inventory saat pembelian dihapus
            $inventory = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                strtolower($pembelian->nama_barang)
            ])->first();

            if ($inventory) {
                $jumlah  = (int) $pembelian->jumlah;
                $kondisi = $pembelian->kondisi_barang;
                $sisaStok = $inventory->stok_tersedia - $jumlah;

                if ($sisaStok <= 0) {
                    // Stok habis → hapus entry inventory
                    InventoryLog::where('inventory_id', $inventory->id)->delete();
                    $inventory->delete();
                } else {
                    $inventory->stok_tersedia = $sisaStok;

                    if ($kondisi === 'baru') {
                        $inventory->stok_baru = max(0, $inventory->stok_baru - $jumlah);
                    } elseif ($kondisi === 'bekas') {
                        $inventory->stok_bekas = max(0, $inventory->stok_bekas - $jumlah);
                    }

                    $inventory->save();
                }
            }

            ActivityLog::record(
                module:   'Pembelian',
                action:   'delete',
                subject:  $pembelian->nama_barang,
                oldValue: [
                    'barang'  => $pembelian->nama_barang,
                    'jumlah'  => $pembelian->jumlah,
                    'kondisi' => $pembelian->kondisi_barang,
                    'total'   => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
                    'tanggal' => $pembelian->tanggal_pembelian,
                    'status'  => $pembelian->status,
                ],
                pageUrl: 'pembelian'
            );

            if ($pembelian->bukti_transaksi) {
                Storage::disk('public')->delete($pembelian->bukti_transaksi);
            }

            $pembelian->delete();
        });

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil dihapus dan stok inventory disesuaikan.');
    }

    // ══════════════════════════════════════
    //  BUY BACK
    // ══════════════════════════════════════

    public function storeBuyBack(Request $request)
    {
        $validated = $request->validate([
            'penjualan_id'      => 'required|exists:penjualans,id',
            'tanggal_pembelian' => 'required|date',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'nama_pelanggan'    => 'required|string|max:255',
            'kondisi_barang'    => 'required|in:baik,bekas,rusak',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $validated['status'] = 'buy_back';

        if ($request->hasFile('bukti_transaksi')) {
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')
                ->store('pembelian/buyback', 'public');
        }

        $namaBarang  = $validated['nama_barang'];
        $jumlah      = (int) $validated['jumlah'];
        $hargaSatuan = (float) $validated['harga_satuan'];
        $pelanggan   = $validated['nama_pelanggan'];
        $kondisi     = $validated['kondisi_barang'];

        DB::transaction(function () use ($validated, $namaBarang, $jumlah, $hargaSatuan, $pelanggan, $kondisi) {

            $pembelian = Pembelian::create($validated);
            $pembelian->refresh();

            $inventory = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                strtolower($namaBarang)
            ])->first();

            if ($inventory) {
                $inventory->stok_tersedia      += $jumlah;
                $inventory->stok_bekas         += $jumlah;
                $inventory->harga_beli_terakhir = $hargaSatuan;
                $inventory->save();
            } else {
                $inventory = Inventory::create([
                    'nama_produk'         => $namaBarang,
                    'kategori'            => null,
                    'satuan'              => 'unit',
                    'stok_tersedia'       => $jumlah,
                    'stok_disewa'         => 0,
                    'stok_baru'           => 0,
                    'stok_bekas'          => $jumlah,
                    'harga_beli_terakhir' => $hargaSatuan,
                ]);
            }

            InventoryLog::create([
                'inventory_id'   => $inventory->id,
                'reference_type' => 'buyback',
                'reference_id'   => $pembelian->id,
                'qty_change'     => $jumlah,
                'kondisi'        => 'bekas',
                'keterangan'     => 'Buy Back dari: ' . $pelanggan,
            ]);

            ActivityLog::record(
                module:   'Pembelian',
                action:   'create',
                subject:  '[Buy Back] ' . $namaBarang . ' dari ' . $pelanggan,
                newValue: [
                    'barang'    => $namaBarang,
                    'pelanggan' => $pelanggan,
                    'kondisi'   => $kondisi,
                    'jumlah'    => $jumlah,
                    'total'     => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
                ],
                pageUrl: 'pembelian/buy-back'
            );
        });

        return redirect()->route('penjualan.index')
            ->with('success', 'Buy back berhasil dicatat dan stok inventory diperbarui.');
    }
}