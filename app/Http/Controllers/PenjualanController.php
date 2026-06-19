<?php

namespace App\Http\Controllers;

use App\Exports\PenjualanExport;
use App\Models\ActivityLog;
use App\Models\DetailPenjualan;
use App\Models\Inventory;
use App\Models\Pembelian;
use App\Models\PembayaranPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PenjualanController extends Controller
{
    // =========================================================
    //  PRIVATE HELPER — sync detail items + update stok
    // =========================================================

    private function syncDetails(Penjualan $penjualan, array $items): void
    {
        $existingDetails = $penjualan->details()->get();
        $existingIds     = $existingDetails->pluck('id')->toArray();
        $submittedIds    = [];
        $totalHarga      = 0;

        foreach ($items as $item) {
            $namaBarang = trim($item['nama_barang'] ?? '');
            if ($namaBarang === '') continue;

            $qtyBaru     = max(1, (int) ($item['qty']          ?? 1));
            $harga       = max(0, (int) ($item['harga_satuan'] ?? 0));
            $diskon      = max(0, min(100, (int) ($item['diskon'] ?? 0)));
            $inventoryId = !empty($item['inventory_id']) ? (int) $item['inventory_id'] : null;
            $kondisi     = in_array($item['kondisi'] ?? '', ['baru', 'bekas'])
                           ? $item['kondisi']
                           : 'baru';
            $subtotal    = (int) round($qtyBaru * $harga * (1 - $diskon / 100));

            $data = [
                'penjualan_id' => $penjualan->id,
                'inventory_id' => $inventoryId,
                'nama_barang'  => $namaBarang,
                'kondisi'      => $kondisi,
                'qty'          => $qtyBaru,
                'satuan'       => $item['satuan'] ?? 'unit',
                'harga_satuan' => $harga,
                'diskon'       => $diskon,
                'subtotal'     => $subtotal,
            ];

            $detailId   = !empty($item['detail_id']) ? (int) $item['detail_id'] : null;
            $detailLama = $detailId
                ? $existingDetails->firstWhere('id', $detailId)
                : null;

            if ($inventoryId) {
                $inv = Inventory::find($inventoryId);
                if ($inv) {
                    $qtyLama     = $detailLama ? (int) $detailLama->qty : 0;
                    $kondisiLama = $detailLama ? $detailLama->kondisi   : $kondisi;
                    $selisih     = $qtyBaru - $qtyLama;

                    if ($detailLama && $kondisiLama !== $kondisi) {
                        if ($qtyLama > 0) $inv->tambahStok($qtyLama, $kondisiLama);
                        $inv->kurangiStok($qtyBaru, $kondisi);
                    } else {
                        if ($selisih > 0)     $inv->kurangiStok($selisih, $kondisi);
                        elseif ($selisih < 0) $inv->tambahStok(abs($selisih), $kondisi);
                    }
                }
            }

            if ($detailLama) {
                DetailPenjualan::where('id', $detailId)->update($data);
                $submittedIds[] = $detailId;
            } else {
                $created        = DetailPenjualan::create($data);
                $submittedIds[] = $created->id;
            }

            $totalHarga += $subtotal;
        }

        // Hapus detail yang dihilangkan — kembalikan stok
        $toDelete = array_diff($existingIds, $submittedIds);
        if (!empty($toDelete)) {
            $detailsToDelete = DetailPenjualan::whereIn('id', $toDelete)->get();
            foreach ($detailsToDelete as $detail) {
                if ($detail->inventory_id) {
                    $inv = Inventory::find($detail->inventory_id);
                    if ($inv) $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru');
                }
            }
            DetailPenjualan::whereIn('id', $toDelete)->delete();
        }

        $penjualan->update(['total_harga' => $totalHarga]);
    }

    // =========================================================
    //  [FIX BUYBACK] PRIVATE HELPER — rollback buyback terkait
    //  Dipanggil saat penjualan dihapus atau dibatalkan.
    //  Kurangi stok inventory sebesar qty buyback, lalu hapus
    //  semua record pembelian buy_back milik penjualan ini.
    // =========================================================

    private function rollbackBuybacks(Penjualan $penjualan): void
    {
        $buybacks = Pembelian::where('penjualan_id', $penjualan->id)
            ->where('status', 'buy_back')
            ->get();

        foreach ($buybacks as $bb) {
            $inv = Inventory::whereRaw('LOWER(nama_produk) = ?', [strtolower($bb->nama_barang)])->first();
            if ($inv) {
                $inv->stok_tersedia = max(0, $inv->stok_tersedia - $bb->jumlah);
                $inv->stok_bekas    = max(0, $inv->stok_bekas    - $bb->jumlah);
                $inv->save();
            }

            if ($bb->bukti_transaksi && Storage::disk('public')->exists($bb->bukti_transaksi)) {
                Storage::disk('public')->delete($bb->bukti_transaksi);
            }

            $bb->delete();
        }
    }

    // =========================================================
    //  INDEX — dengan filter tanggal
    // =========================================================

    public function index(Request $request)
    {
        $search   = $request->input('search', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo   = $request->input('date_to', '');
        $perPage  = in_array($request->input('per_page'), [5, 10, 25, 50])
                    ? (int) $request->input('per_page')
                    : 10;

        $penjualans = Penjualan::with('details')
            ->when($search, function ($q) use ($search) {
                $q->where('nama_pelanggan',      'like', "%{$search}%")
                  ->orWhere('nomor_telepon',     'like', "%{$search}%")
                  ->orWhere('alamat_pelanggan',  'like', "%{$search}%")
                  ->orWhere('jenis_pembayaran',  'like', "%{$search}%")
                  ->orWhere('keterangan',        'like', "%{$search}%")
                  ->orWhere('tanggal_penjualan', 'like', "%{$search}%")
                  ->orWhere('status_pembayaran', 'like', "%{$search}%");
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('tanggal_penjualan', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('tanggal_penjualan', '<=', $dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penjualan.index', compact(
            'penjualans', 'search', 'perPage', 'dateFrom', 'dateTo'
        ));
    }

    // =========================================================
    //  EXPORT XLSX
    // =========================================================

    public function export(Request $request)
    {
        $search           = $request->input('search', '');
        $dateFrom         = $request->input('date_from')         ?: null;
        $dateTo           = $request->input('date_to')           ?: null;
        $statusPembayaran = $request->input('status_pembayaran') ?: null;
        $statusTransaksi  = $request->input('status_transaksi')  ?: null;

        if ($dateFrom && $dateTo) {
            $rangeLabel = \Carbon\Carbon::parse($dateFrom)->format('d-m-Y')
                . '_sd_'
                . \Carbon\Carbon::parse($dateTo)->format('d-m-Y');
        } else {
            $rangeLabel = now()->format('Ymd_His');
        }

        $filename = 'penjualan_' . $rangeLabel . '.xlsx';

        return Excel::download(
            new PenjualanExport($search, $dateFrom, $dateTo, $statusPembayaran, $statusTransaksi),
            $filename
        );
    }

    // =========================================================
    //  CREATE & STORE
    // =========================================================

    public function create()
    {
        return view('admin.penjualan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan'       => 'required|string|max:255',
            'nomor_telepon'        => 'nullable|string|max:20',
            'alamat_pelanggan'     => 'required|string',
            'tanggal_penjualan'    => 'required|date',
            'diskon_global'        => 'nullable|integer|min:0',
            'foto_bukti'           => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'keterangan'           => 'nullable|string',
            'metode_pembayaran'    => 'required|in:cash,dp,transfer',
            'metode_bayar_awal'    => 'required|in:cash,transfer,qris',
            'jumlah_bayar_awal'    => 'required|integer|min:0',
            'tanggal_bayar_awal'   => 'required|date',
            'jasa_pengiriman'      => 'nullable|in:ambil_sendiri,gosend_grab,rental_mobil',
            'harga_pengiriman'     => 'nullable|integer|min:0',
            'jasa_instalasi'       => 'nullable|integer|min:0',
            'items'                => 'required|array|min:1',
            'items.*.inventory_id' => 'nullable|integer|exists:inventories,id',
            'items.*.nama_barang'  => 'required|string|max:255',
            'items.*.kondisi'      => 'required|in:baru,bekas',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.satuan'       => 'required|string|max:50',
            'items.*.harga_satuan' => 'required|integer|min:0',
            'items.*.diskon'       => 'nullable|integer|min:0|max:100',
        ]);

        $fotoBukti = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoBukti = $request->file('foto_bukti')
                ->store('penjualan/bukti', 'public');
        }

        $penjualan = DB::transaction(function () use ($validated, $fotoBukti) {

            $diskonGlobal    = max(0, (int) ($validated['diskon_global']    ?? 0));
            $hargaPengiriman = max(0, (int) ($validated['harga_pengiriman'] ?? 0));
            $jasaInstalasi   = max(0, (int) ($validated['jasa_instalasi']   ?? 0));

            $jasaPengiriman = $validated['jasa_pengiriman'] ?? 'ambil_sendiri';
            if ($jasaPengiriman === 'ambil_sendiri') {
                $hargaPengiriman = 0;
            }

            $penjualan = Penjualan::create([
                'user_id'           => auth()->id(),
                'nama_pelanggan'    => $validated['nama_pelanggan'],
                'nomor_telepon'     => $validated['nomor_telepon']  ?? null,
                'alamat_pelanggan'  => $validated['alamat_pelanggan'],
                'tanggal_penjualan' => $validated['tanggal_penjualan'],
                'jenis_pembayaran'  => $validated['metode_bayar_awal'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'diskon_global'     => 0,
                'total_harga'       => 0,
                'total_terbayar'    => 0,
                'jasa_pengiriman'   => $jasaPengiriman,
                'harga_pengiriman'  => $hargaPengiriman,
                'jasa_instalasi'    => $jasaInstalasi,
                'status_pembayaran' => 'belum_lunas',
                'status_transaksi'  => 'aktif',
                'foto_bukti'        => $fotoBukti,
                'keterangan'        => $validated['keterangan'] ?? null,
            ]);

            $this->syncDetails($penjualan, $validated['items']);

            $penjualan->refresh();
            $diskonGlobalSafe = min($diskonGlobal, (int) $penjualan->total_harga);
            $penjualan->update(['diskon_global' => $diskonGlobalSafe]);

            $jumlahBayar = (int) $validated['jumlah_bayar_awal'];
            if ($jumlahBayar > 0) {
                $tipe = $validated['metode_pembayaran'] === 'dp'
                    ? 'dp'
                    : 'pelunasan';

                PembayaranPenjualan::create([
                    'penjualan_id'  => $penjualan->id,
                    'created_by'    => auth()->id(),
                    'tipe'          => $tipe,
                    'metode'        => $validated['metode_bayar_awal'],
                    'jumlah_bayar'  => $jumlahBayar,
                    'tanggal_bayar' => $validated['tanggal_bayar_awal'],
                    'keterangan'    => 'Pembayaran awal saat input transaksi',
                ]);
            }

            return $penjualan;
        });

        ActivityLog::record(
            module:   'Penjualan',
            action:   'create',
            subject:  'No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            newValue: [
                'pelanggan' => $penjualan->nama_pelanggan,
                'barang'    => collect($validated['items'])->pluck('nama_barang')->implode(', '),
                'tanggal'   => $penjualan->tanggal_penjualan->format('d M Y'),
                'total'     => 'Rp ' . number_format($penjualan->fresh()->total_tagihan, 0, ',', '.'),
            ],
            pageUrl: 'penjualan'
        );

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', 'Data penjualan berhasil ditambahkan.');
    }

    // =========================================================
    //  SHOW
    // =========================================================

    public function show(string $id)
    {
        $penjualan = Penjualan::with([
            'details.inventory',
            'pembayarans.createdBy',
        ])->findOrFail($id);

        return view('admin.penjualan.show', compact('penjualan'));
    }

    // =========================================================
    //  EDIT & UPDATE
    // =========================================================

    public function edit(string $id)
    {
        $penjualan = Penjualan::with('details.inventory')->findOrFail($id);

        if (method_exists($penjualan, 'isBatal') && $penjualan->isBatal()) {
            return redirect()->route('penjualan.show', $id)
                ->with('error', 'Transaksi yang sudah dibatalkan tidak dapat diedit.');
        }

        $existingItems = $penjualan->details->map(function ($d) {
            return [
                'detail_id'        => $d->id,
                'inventory_id'     => $d->inventory_id,
                'nama_barang'      => $d->nama_barang,
                'kondisi'          => $d->kondisi      ?? 'baru',
                'qty'              => $d->qty,
                'satuan'           => $d->satuan        ?? 'unit',
                'harga_satuan'     => $d->harga_satuan  ?? 0,
                'diskon'           => $d->diskon        ?? 0,
                'stok_baru'        => $d->inventory ? ($d->inventory->stok_baru        ?? 0) : 0,
                'stok_bekas'       => $d->inventory ? ($d->inventory->stok_bekas       ?? 0) : 0,
                'harga_jual_baru'  => $d->inventory ? ($d->inventory->harga_jual_baru  ?? 0) : 0,
                'harga_jual_bekas' => $d->inventory ? ($d->inventory->harga_jual_bekas ?? 0) : 0,
            ];
        })->values()->toArray();

        return view('admin.penjualan.edit', compact('penjualan', 'existingItems'));
    }

    public function update(Request $request, string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if (method_exists($penjualan, 'isBatal') && $penjualan->isBatal()) {
            return redirect()->route('penjualan.show', $id)
                ->with('error', 'Transaksi yang sudah dibatalkan tidak dapat diubah.');
        }

        $oldData = [
            'pelanggan' => $penjualan->nama_pelanggan,
            'tanggal'   => $penjualan->tanggal_penjualan?->format('d M Y'),
            'total'     => 'Rp ' . number_format($penjualan->total_tagihan ?? 0, 0, ',', '.'),
        ];

        $validated = $request->validate([
            'nama_pelanggan'       => 'required|string|max:255',
            'nomor_telepon'        => 'nullable|string|max:20',
            'alamat_pelanggan'     => 'required|string',
            'tanggal_penjualan'    => 'required|date',
            'jenis_pembayaran'     => 'required|string|max:30',
            'diskon_global'        => 'nullable|integer|min:0',
            'foto_bukti'           => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'keterangan'           => 'nullable|string',
            'jasa_pengiriman'      => 'nullable|in:ambil_sendiri,gosend_grab,rental_mobil',
            'harga_pengiriman'     => 'nullable|integer|min:0',
            'jasa_instalasi'       => 'nullable|integer|min:0',
            'items'                => 'required|array|min:1',
            'items.*.detail_id'    => 'nullable|integer',
            'items.*.inventory_id' => 'nullable|integer|exists:inventories,id',
            'items.*.nama_barang'  => 'required|string|max:255',
            'items.*.kondisi'      => 'required|in:baru,bekas',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.satuan'       => 'required|string|max:50',
            'items.*.harga_satuan' => 'required|integer|min:0',
            'items.*.diskon'       => 'nullable|integer|min:0|max:100',
        ]);

        DB::transaction(function () use ($validated, $request, $penjualan) {

            if ($request->hasFile('foto_bukti')) {
                if ($penjualan->foto_bukti &&
                    Storage::disk('public')->exists($penjualan->foto_bukti)) {
                    Storage::disk('public')->delete($penjualan->foto_bukti);
                }
                $validated['foto_bukti'] = $request->file('foto_bukti')
                    ->store('penjualan/bukti', 'public');
            }

            $jasaPengiriman  = $validated['jasa_pengiriman']  ?? 'ambil_sendiri';
            $hargaPengiriman = max(0, (int) ($validated['harga_pengiriman'] ?? 0));
            $jasaInstalasi   = max(0, (int) ($validated['jasa_instalasi']   ?? 0));

            if ($jasaPengiriman === 'ambil_sendiri') {
                $hargaPengiriman = 0;
            }

            $penjualan->update(collect($validated)
                ->except(['items', 'diskon_global'])
                ->merge([
                    'jasa_pengiriman'  => $jasaPengiriman,
                    'harga_pengiriman' => $hargaPengiriman,
                    'jasa_instalasi'   => $jasaInstalasi,
                    'diskon_global'    => 0,
                ])
                ->toArray()
            );

            $this->syncDetails($penjualan, $validated['items']);

            $penjualan->refresh();
            $diskonGlobal     = max(0, (int) ($validated['diskon_global'] ?? 0));
            $diskonGlobalSafe = min($diskonGlobal, (int) $penjualan->total_harga);
            $penjualan->update(['diskon_global' => $diskonGlobalSafe]);

            if (method_exists($penjualan, 'syncStatusPembayaran')) {
                $penjualan->fresh()->syncStatusPembayaran();
            }
        });

        ActivityLog::record(
            module:   'Penjualan',
            action:   'update',
            subject:  'No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            oldValue: $oldData,
            newValue: [
                'pelanggan' => $penjualan->fresh()->nama_pelanggan,
                'tanggal'   => $penjualan->fresh()->tanggal_penjualan?->format('d M Y'),
                'total'     => 'Rp ' . number_format($penjualan->fresh()->total_tagihan ?? 0, 0, ',', '.'),
            ],
            pageUrl: 'penjualan/' . $penjualan->id . '/edit'
        );

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', 'Data penjualan berhasil diperbarui.');
    }

    // =========================================================
    //  TAMBAH PEMBAYARAN
    // =========================================================

    public function tambahPembayaran(Request $request, string $penjualan)
    {
        $penjualan = Penjualan::findOrFail($penjualan);

        if (method_exists($penjualan, 'isBatal') && $penjualan->isBatal()) {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }
        if (method_exists($penjualan, 'isLunas') && $penjualan->isLunas()) {
            return back()->with('error', 'Transaksi sudah lunas.');
        }

        $validated = $request->validate([
            'tipe'          => 'required|in:dp,pelunasan,cicilan',
            'metode'        => 'required|in:cash,transfer,qris',
            'jumlah_bayar'  => 'required|integer|min:1',
            'tanggal_bayar' => 'required|date',
            'keterangan'    => 'nullable|string|max:500',
            'foto_bukti'    => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $sisaTagihan = $penjualan->sisa_tagihan ?? 0;
        if ((int) $validated['jumlah_bayar'] > $sisaTagihan) {
            return back()
                ->withInput()
                ->with('error', 'Jumlah bayar melebihi sisa tagihan (Rp ' .
                    number_format($sisaTagihan, 0, ',', '.') . ').');
        }

        $fotoBukti = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoBukti = $request->file('foto_bukti')
                ->store('penjualan/pembayaran', 'public');
        }

        PembayaranPenjualan::create([
            'penjualan_id'  => $penjualan->id,
            'created_by'    => auth()->id(),
            'tipe'          => $validated['tipe'],
            'metode'        => $validated['metode'],
            'jumlah_bayar'  => $validated['jumlah_bayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'keterangan'    => $validated['keterangan'] ?? null,
            'foto_bukti'    => $fotoBukti,
        ]);

        ActivityLog::record(
            module:   'Penjualan',
            action:   'update',
            subject:  'Pembayaran No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            newValue: [
                'jumlah_bayar' => 'Rp ' . number_format($validated['jumlah_bayar'], 0, ',', '.'),
                'tipe'         => $validated['tipe'],
                'metode'       => $validated['metode'],
            ],
            pageUrl: 'penjualan/' . $penjualan->id
        );

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    // =========================================================
    //  HAPUS PEMBAYARAN
    // =========================================================

    public function hapusPembayaran(string $penjualan, string $pembayaran)
    {
        $penjualan  = Penjualan::findOrFail($penjualan);
        $pembayaran = PembayaranPenjualan::where('penjualan_id', $penjualan->id)
                        ->findOrFail($pembayaran);

        if (method_exists($penjualan, 'isBatal') && $penjualan->isBatal()) {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }

        if ($pembayaran->foto_bukti &&
            Storage::disk('public')->exists($pembayaran->foto_bukti)) {
            Storage::disk('public')->delete($pembayaran->foto_bukti);
        }

        $pembayaran->delete();

        if (method_exists($penjualan, 'syncStatusPembayaran')) {
            $penjualan->syncStatusPembayaran();
        }

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', 'Data pembayaran berhasil dihapus.');
    }

    // =========================================================
    //  BATALKAN TRANSAKSI
    // =========================================================

    public function batalkan(Request $request, string $penjualan)
    {
        $penjualan = Penjualan::with('details')->findOrFail($penjualan);

        if (method_exists($penjualan, 'isBatal') && $penjualan->isBatal()) {
            return back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        $request->validate([
            'catatan_pembatalan' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($penjualan, $request) {
            // Kembalikan stok dari detail penjualan
            foreach ($penjualan->details as $detail) {
                if ($detail->inventory_id) {
                    $inv = Inventory::find($detail->inventory_id);
                    if ($inv) $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru');
                }
            }

            // [FIX BUYBACK] Rollback semua buyback terkait penjualan ini
            $this->rollbackBuybacks($penjualan);

            $penjualan->update([
                'status_transaksi'   => 'batal',
                'catatan_pembatalan' => $request->input('catatan_pembatalan'),
                'status_pembayaran'  => $penjualan->status_pembayaran,
            ]);
        });

        ActivityLog::record(
            module:   'Penjualan',
            action:   'update',
            subject:  'Batalkan No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            newValue: ['catatan' => $request->input('catatan_pembatalan')],
            pageUrl:  'penjualan/' . $penjualan->id
        );

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', 'Transaksi berhasil dibatalkan.');
    }

    // =========================================================
    //  DESTROY
    // =========================================================

    public function destroy(string $id)
    {
        $penjualan = Penjualan::with('details')->findOrFail($id);

        DB::transaction(function () use ($penjualan) {
            $isBatal = method_exists($penjualan, 'isBatal') && $penjualan->isBatal();

            if (!$isBatal) {
                // Kembalikan stok dari detail penjualan
                foreach ($penjualan->details as $detail) {
                    if ($detail->inventory_id) {
                        $inv = Inventory::find($detail->inventory_id);
                        if ($inv) $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru');
                    }
                }
            }

            // [FIX BUYBACK] Rollback buyback meski penjualan sudah batal sekalipun.
            // Buyback tetap harus dihapus karena penjualan induknya sudah tidak ada.
            $this->rollbackBuybacks($penjualan);

            if ($penjualan->foto_bukti &&
                Storage::disk('public')->exists($penjualan->foto_bukti)) {
                Storage::disk('public')->delete($penjualan->foto_bukti);
            }

            if (method_exists($penjualan, 'pembayarans')) {
                $penjualan->pembayarans()->each(function ($p) {
                    if ($p->foto_bukti &&
                        Storage::disk('public')->exists($p->foto_bukti)) {
                        Storage::disk('public')->delete($p->foto_bukti);
                    }
                    $p->delete();
                });
            }

            $penjualan->delete();
        });

        ActivityLog::record(
            module:   'Penjualan',
            action:   'delete',
            subject:  'No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            oldValue: [
                'pelanggan' => $penjualan->nama_pelanggan,
                'tanggal'   => $penjualan->tanggal_penjualan?->format('d M Y'),
                'total'     => 'Rp ' . number_format($penjualan->total_tagihan ?? 0, 0, ',', '.'),
            ],
            pageUrl: 'penjualan'
        );

        return redirect()->route('penjualan.index')
            ->with('success', 'Data penjualan berhasil dihapus.');
    }

    // =========================================================
    //  CETAK INVOICE
    // =========================================================

    public function invoice(string $id)
    {
        $penjualan = Penjualan::with('details.inventory')->findOrFail($id);
        return view('admin.penjualan.cetak.invoice', compact('penjualan'));
    }
}