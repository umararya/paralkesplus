<?php
// app/Http/Controllers/PembelianController.php

namespace App\Http\Controllers;

use App\Exports\PembelianExport;
use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Pembelian;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search', '');
        $filter   = $request->input('filter', 'semua');
        $dateFrom = $request->input('date_from', '');
        $dateTo   = $request->input('date_to', '');
        $perPage  = in_array($request->input('per_page'), [5, 10, 25, 50])
                    ? (int) $request->input('per_page')
                    : 10;

        $query = Pembelian::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('no_invoice', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                        ->orWhere('tanggal_pembelian', 'like', "%{$search}%")
                        ->orWhereRaw('CAST(jumlah AS CHAR) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('CAST(harga_satuan AS CHAR) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('CAST(total AS CHAR) LIKE ?', ["%{$search}%"]);
                });
            })
            ->when($filter !== 'semua', function ($q) use ($filter) {
                $q->where('status', $filter);
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('tanggal_pembelian', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('tanggal_pembelian', '<=', $dateTo);
            });

        $pembelians       = (clone $query)->orderBy('tanggal_pembelian', 'desc')->paginate($perPage)->withQueryString();
        $totalKeseluruhan = (clone $query)->sum('total');

        $countSemua   = Pembelian::count();
        $countNormal  = Pembelian::where('status', 'normal')->count();
        $countBuyBack = Pembelian::where('status', 'buy_back')->count();

        return view('admin.pembelian.index', compact(
            'pembelians', 'search', 'perPage', 'totalKeseluruhan',
            'filter', 'countSemua', 'countNormal', 'countBuyBack',
            'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $search   = $request->input('search', '');
        $filter   = $request->input('filter', 'semua');
        $dateFrom = $request->input('date_from', '');
        $dateTo   = $request->input('date_to', '');
        $filename = 'pembelian_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PembelianExport($search, $filter, $dateFrom, $dateTo), $filename);
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
            'no_invoice'        => 'nullable|string|max:100',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'kondisi_barang'    => 'required|in:baru,bekas',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|file|mimes:jpeg,png,webp,pdf|max:10240',
            'file_invoice'      => 'nullable|file|mimes:jpeg,png,pdf|max:10240',
        ]);

        $validated['status'] = 'normal';

        if ($request->hasFile('bukti_transaksi')) {
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')
                ->store('pembelian/bukti', 'public');
        }

        if ($request->hasFile('file_invoice')) {
            $validated['file_invoice'] = $request->file('file_invoice')
                ->store('pembelian/invoice', 'public');
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
                    'no_invoice'   => $pembelian->no_invoice ?? '-',
                    'file_invoice' => $pembelian->file_invoice ? 'Ada' : 'Tidak ada',
                    'barang'       => $namaBarang,
                    'jumlah'       => $jumlah,
                    'kondisi'      => $kondisi,
                    'total'        => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
                    'tanggal'      => $pembelian->tanggal_pembelian,
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
            'no_invoice'        => 'nullable|string|max:100',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'kondisi_barang'    => 'required|in:baru,bekas',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|file|mimes:jpeg,png,webp,pdf|max:10240',
            'file_invoice'      => 'nullable|file|mimes:jpeg,png,pdf|max:10240',
        ]);

        $oldNama    = $pembelian->nama_barang;
        $oldJumlah  = (int) $pembelian->jumlah;
        $oldKondisi = $pembelian->kondisi_barang;

        $oldData = [
            'no_invoice'   => $pembelian->no_invoice ?? '-',
            'file_invoice' => $pembelian->file_invoice ? 'Ada' : 'Tidak ada',
            'barang'       => $oldNama,
            'jumlah'       => $oldJumlah,
            'kondisi'      => $oldKondisi,
            'total'        => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
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

        if ($request->hasFile('file_invoice')) {
            if ($pembelian->file_invoice) {
                Storage::disk('public')->delete($pembelian->file_invoice);
            }
            $validated['file_invoice'] = $request->file('file_invoice')
                ->store('pembelian/invoice', 'public');
        } elseif ($request->has('hapus_file_invoice')) {
            if ($pembelian->file_invoice) {
                Storage::disk('public')->delete($pembelian->file_invoice);
            }
            $validated['file_invoice'] = null;
        } else {
            unset($validated['file_invoice']);
        }

        $newNama     = $validated['nama_barang'];
        $newJumlah   = (int) $validated['jumlah'];
        $newKondisi  = $validated['kondisi_barang'];
        $newHarga    = (float) $validated['harga_satuan'];
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
                    $inventory->stok_tersedia       = max(0, $inventory->stok_tersedia + $diffJumlah);
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
                        $invLama->stok_tersedia = 0;
                        $invLama->stok_baru     = 0;
                        $invLama->stok_bekas    = 0;
                        $invLama->save();
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
                    'no_invoice'   => $pembelian->no_invoice ?? '-',
                    'file_invoice' => $pembelian->file_invoice ? 'Ada' : 'Tidak ada',
                    'barang'       => $newNama,
                    'jumlah'       => $newJumlah,
                    'kondisi'      => $newKondisi,
                    'total'        => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
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
                    $inventory->stok_tersedia = 0;
                    $inventory->stok_baru     = 0;
                    $inventory->stok_bekas    = 0;
                    $inventory->save();
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
                    'no_invoice'   => $pembelian->no_invoice ?? '-',
                    'file_invoice' => $pembelian->file_invoice ? 'Ada' : 'Tidak ada',
                    'barang'       => $pembelian->nama_barang,
                    'jumlah'       => $pembelian->jumlah,
                    'kondisi'      => $pembelian->kondisi_barang,
                    'total'        => 'Rp ' . number_format($pembelian->attributes['total'] ?? 0, 0, ',', '.'),
                    'tanggal'      => $pembelian->tanggal_pembelian,
                    'status'       => $pembelian->status,
                ],
                pageUrl: 'pembelian'
            );

            if ($pembelian->bukti_transaksi) {
                Storage::disk('public')->delete($pembelian->bukti_transaksi);
            }
            if ($pembelian->file_invoice) {
                Storage::disk('public')->delete($pembelian->file_invoice);
            }

            $pembelian->delete();
        });

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil dihapus dan stok inventory disesuaikan.');
    }

    public function storeBuyBack(Request $request)
    {
        $request->validate([
            'penjualan_id'          => 'required|exists:penjualans,id',
            'keterangan'            => 'nullable|string|max:500',
            'items'                 => 'required|array|min:1',
            'items.*.detail_id'     => 'required|integer|exists:detail_penjualans,id',
            'items.*.qty_buyback'   => 'required|integer|min:1',
            // nullable: fallback ke 50% kalau tidak dikirim dari form
            'items.*.harga_buyback' => 'nullable|numeric|min:0',
        ]);

        $penjualanId = (int) $request->input('penjualan_id');
        $keterangan  = $request->input('keterangan');
        $items       = $request->input('items', []);

        $penjualan = \App\Models\Penjualan::with('details')->findOrFail($penjualanId);

        DB::transaction(function () use ($penjualan, $items, $keterangan, $penjualanId) {

            foreach ($items as $item) {
                $detailId = (int) $item['detail_id'];
                $detail   = DetailPenjualan::findOrFail($detailId);

                // BUG 5 FIX: cek sudah berapa qty di-buyback sebelumnya
                $sudahBuyback = Pembelian::where('status', 'buy_back')
                    ->where('penjualan_id', $penjualanId)
                    ->where('nama_barang', $detail->nama_barang)
                    ->sum('jumlah');

                $maxBuyback = $detail->qty - $sudahBuyback;

                if ($maxBuyback < 1) continue;

                $qtyBuyback = min((int) $item['qty_buyback'], $maxBuyback);

                if ($qtyBuyback < 1) continue;

                $namaBarang  = $detail->nama_barang;
                $hargaAsli   = (float) $detail->harga_satuan;

                // BUG 6 FIX: pakai nilai dari form kalau ada, fallback 50% kalau tidak
                $hargaBuyback = (isset($item['harga_buyback']) && $item['harga_buyback'] !== '')
                    ? (float) $item['harga_buyback']
                    : round($hargaAsli * 0.5);

                $totalBuyback = $hargaBuyback * $qtyBuyback;

                $pembelian = Pembelian::create([
                    'penjualan_id'      => $penjualanId,
                    'tanggal_pembelian' => now()->toDateString(),
                    'no_invoice'        => null,
                    'file_invoice'      => null,
                    'nama_barang'       => $namaBarang,
                    'jumlah'            => $qtyBuyback,
                    'harga_satuan'      => $hargaBuyback,
                    'total'             => $totalBuyback,
                    'nama_pelanggan'    => $penjualan->nama_pelanggan,
                    'kondisi_barang'    => 'bekas',
                    'keterangan'        => $keterangan ?? 'Buy Back dari penjualan #' . $penjualanId,
                    'bukti_transaksi'   => null,
                    'status'            => 'buy_back',
                ]);

                $inventory = Inventory::whereRaw('LOWER(nama_produk) = ?', [
                    strtolower($namaBarang)
                ])->first();

                if ($inventory) {
                    $inventory->stok_tersedia += $qtyBuyback;
                    $inventory->stok_bekas    += $qtyBuyback;
                    $inventory->save();
                } else {
                    $inventory = Inventory::create([
                        'nama_produk'         => $namaBarang,
                        'kategori'            => null,
                        'satuan'              => $detail->satuan ?? 'unit',
                        'stok_tersedia'       => $qtyBuyback,
                        'stok_disewa'         => 0,
                        'stok_baru'           => 0,
                        'stok_bekas'          => $qtyBuyback,
                        'harga_beli_terakhir' => $hargaBuyback,
                    ]);
                }

                InventoryLog::create([
                    'inventory_id'   => $inventory->id,
                    'reference_type' => 'buyback',
                    'reference_id'   => $pembelian->id,
                    'qty_change'     => $qtyBuyback,
                    'kondisi'        => 'bekas',
                    'keterangan'     => 'Buy Back dari: ' . $penjualan->nama_pelanggan . ' — ' . $namaBarang,
                ]);

                ActivityLog::record(
                    module:   'Pembelian',
                    action:   'create',
                    subject:  '[Buy Back] ' . $namaBarang . ' dari ' . $penjualan->nama_pelanggan,
                    newValue: [
                        'barang'        => $namaBarang,
                        'pelanggan'     => $penjualan->nama_pelanggan,
                        'kondisi'       => 'bekas',
                        'qty'           => $qtyBuyback,
                        'harga_buyback' => 'Rp ' . number_format($hargaBuyback, 0, ',', '.'),
                        'total_bayar'   => 'Rp ' . number_format($totalBuyback, 0, ',', '.'),
                    ],
                    pageUrl: 'penjualan'
                );
            }
        });

        return redirect()->route('penjualan.index')
            ->with('success', 'Buy back berhasil! Stok bekas inventory sudah diperbarui.');
    }

    public function invoice(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        abort_if($pembelian->status !== 'buy_back', 404, 'Invoice hanya tersedia untuk transaksi buy back.');

        return view('admin.pembelian.cetak.invoice', compact('pembelian'));
    }
}