<?php
// app/Http/Controllers/PembelianController.php

namespace App\Http\Controllers;

use App\Exports\PembelianExport;                // ← TAMBAH
use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;            // ← TAMBAH

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

    // =========================================================
    //  EXPORT XLSX  ← TAMBAH METHOD INI
    // =========================================================

    public function export(Request $request)
    {
        $search   = $request->input('search', '');
        $filter   = $request->input('filter', 'semua');
        $filename = 'pembelian_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PembelianExport($search, $filter), $filename);
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

        $oldNama    = $pembelian->nama_barang;
        $oldJumlah  = (int) $pembelian->jumlah;
        $oldKondisi = $pembelian->kondisi_barang;

        $oldData = [
            'barang'  => $oldNama,
            'jumlah'  => $oldJumlah,
            'kondisi' => $oldKondisi,
            'total'   => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
        ];

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
        $namaBerubah = strtolower($oldNama) !== strtolower($newNama);

        DB::transaction(function () use (
            $pembelian, $validated,
            $oldNama, $oldJumlah, $oldKondisi,
            $newNama, $newJumlah, $newKondisi, $newHarga,
            $namaBerubah, $oldData
        ) {
            $pembelian->update($validated);
            $pembelian->refresh();

            if (!$namaBerubah) {
                $inventory = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                    strtolower($newNama)
                ])->first();

                if ($inventory) {
                    $diffJumlah = $newJumlah - $oldJumlah;
                    $inventory->stok_tersedia      = max(0, $inventory->stok_tersedia + $diffJumlah);
                    $inventory->harga_beli_terakhir = $newHarga;

                    if ($oldKondisi === $newKondisi) {
                        if ($newKondisi === 'baru') {
                            $inventory->stok_baru  = max(0, $inventory->stok_baru + $diffJumlah);
                        } else {
                            $inventory->stok_bekas = max(0, $inventory->stok_bekas + $diffJumlah);
                        }
                    } else {
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
            } else {
                $invLama = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                    strtolower($oldNama)
                ])->first();

                if ($invLama) {
                    $sisaStok = $invLama->stok_tersedia - $oldJumlah;

                    if ($sisaStok <= 0) {
                        InventoryLog::where('inventory_id', $invLama->id)->delete();
                        $invLama->delete();
                    } else {
                        $invLama->stok_tersedia = $sisaStok;

                        if ($oldKondisi === 'baru') {
                            $invLama->stok_baru = max(0, $invLama->stok_baru - $oldJumlah);
                        } else {
                            $invLama->stok_bekas = max(0, $invLama->stok_bekas - $oldJumlah);
                        }

                        $invLama->save();
                    }
                }

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
            $inventory = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                strtolower($pembelian->nama_barang)
            ])->first();

            if ($inventory) {
                $jumlah   = (int) $pembelian->jumlah;
                $kondisi  = $pembelian->kondisi_barang;
                $sisaStok = $inventory->stok_tersedia - $jumlah;

                if ($sisaStok <= 0) {
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
    //  BUY BACK — MULTI ITEM
    // ══════════════════════════════════════

    public function storeBuyBack(Request $request)
    {
        $request->validate([
            'penjualan_id'         => 'required|exists:penjualans,id',
            'tanggal_pembelian'    => 'required|date',
            'nama_pelanggan'       => 'required|string|max:255',
            'kondisi_barang'       => 'required|in:baik,bekas,rusak',
            'keterangan'           => 'nullable|string',
            'bukti_transaksi'      => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'items'                => 'required|array|min:1',
            'items.*.nama_barang'  => 'required|string|max:150',
            'items.*.jumlah'       => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        $tanggal     = $request->input('tanggal_pembelian');
        $pelanggan   = $request->input('nama_pelanggan');
        $kondisi     = $request->input('kondisi_barang');
        $keterangan  = $request->input('keterangan');
        $penjualanId = $request->input('penjualan_id');
        $items       = $request->input('items', []);

        $fotoPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $fotoPath = $request->file('bukti_transaksi')
                ->store('pembelian/buyback', 'public');
        }

        DB::transaction(function () use (
            $items, $tanggal, $pelanggan, $kondisi,
            $keterangan, $penjualanId, $fotoPath
        ) {
            foreach ($items as $item) {
                $namaBarang  = trim($item['nama_barang']);
                $jumlah      = (int) $item['jumlah'];
                $hargaSatuan = (float) $item['harga_satuan'];
                $total       = $jumlah * $hargaSatuan;

                if (empty($namaBarang) || $jumlah < 1) {
                    continue;
                }

                $pembelian = Pembelian::create([
                    'penjualan_id'      => $penjualanId,
                    'tanggal_pembelian' => $tanggal,
                    'nama_barang'       => $namaBarang,
                    'jumlah'            => $jumlah,
                    'harga_satuan'      => $hargaSatuan,
                    'total'             => $total,
                    'nama_pelanggan'    => $pelanggan,
                    'kondisi_barang'    => $kondisi,
                    'keterangan'        => $keterangan,
                    'bukti_transaksi'   => $fotoPath,
                    'status'            => 'buy_back',
                ]);

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
                    'keterangan'     => 'Buy Back dari: ' . $pelanggan . ' — ' . $namaBarang,
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
                        'total'     => 'Rp ' . number_format($total, 0, ',', '.'),
                    ],
                    pageUrl: 'pembelian/buy-back'
                );
            }
        });

        return redirect()->route('penjualan.index')
            ->with('success', 'Buy back berhasil dicatat. Tiap produk tersimpan di data pembelian dan stok bekas inventory diperbarui.');
    }
}