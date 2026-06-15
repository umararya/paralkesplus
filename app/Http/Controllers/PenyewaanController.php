<?php
// app/Http/Controllers/PenyewaanController.php

namespace App\Http\Controllers;

use App\Exports\PenyewaanExport;
use App\Models\ActivityLog;
use App\Models\DetailPenyewaan;
use App\Models\Inventory;
use App\Models\Penyewaan;
use App\Models\PenyewaanExtend;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PenyewaanController extends Controller
{
    // =========================================================
    //  PRIVATE HELPER — sync status otomatis
    // =========================================================

    private function syncStatus(Penyewaan $item): void
    {
        if (in_array($item->status, ['selesai', 'dibatalkan'])) return;

        $sisaHari = $item->sisa_hari;

        if ($sisaHari <= 0) {
            $item->update(['status' => 'selesai']);
            $this->kembalikanStok($item);
            return;
        }

        if ($sisaHari <= 7 && $item->status === 'berjalan') {
            $item->update(['status' => 'segera_konfirmasi']);
            return;
        }

        if ($sisaHari > 7 && $item->status === 'segera_konfirmasi') {
            $item->update(['status' => 'berjalan']);
        }
    }

    // =========================================================
    //  PRIVATE HELPER — kembalikan stok semua detail penyewaan
    // =========================================================

    private function kembalikanStok(Penyewaan $penyewaan): void
    {
        if (! $penyewaan->relationLoaded('details')) {
            $penyewaan->load('details');
        }

        foreach ($penyewaan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->tambahStok($detail->qty, 'bekas', true);
                }
            }
        }
    }

    // =========================================================
    //  PRIVATE HELPER — kurangi stok (saat restore)
    // =========================================================

    private function kurangiStokPenyewaan(Penyewaan $penyewaan): void
    {
        if (! $penyewaan->relationLoaded('details')) {
            $penyewaan->load('details');
        }

        foreach ($penyewaan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->kurangiStok($detail->qty, $detail->kondisi ?? 'baru', true);
                }
            }
        }
    }

    // =========================================================
    //  PRIVATE HELPER — simpan / sync detail items + update stok
    // =========================================================

    private function syncDetails(Penyewaan $penyewaan, array $items): void
    {
        $existingDetails = $penyewaan->details()->get();
        $existingIds     = $existingDetails->pluck('id')->toArray();
        $submittedIds    = [];
        $totalSewa       = 0;

        foreach ($items as $item) {
            $namaAlat = trim($item['nama_alat'] ?? '');
            if ($namaAlat === '') continue;

            $qtyBaru     = max(1, (int) ($item['qty']          ?? 1));
            $harga       = max(0, (int) ($item['harga_satuan'] ?? 0));
            $diskon      = max(0, min(100, (int) ($item['diskon'] ?? 0)));
            $inventoryId = !empty($item['inventory_id']) ? (int) $item['inventory_id'] : null;
            $kondisi     = in_array($item['kondisi'] ?? '', ['baru', 'bekas'])
                           ? $item['kondisi']
                           : 'baru';
            $subtotal    = (int) round($qtyBaru * $harga * (1 - $diskon / 100));

            $data = [
                'penyewaan_id' => $penyewaan->id,
                'inventory_id' => $inventoryId,
                'kondisi'      => $kondisi,
                'nama_alat'    => $namaAlat,
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
                    $kondisiLama = $detailLama ? ($detailLama->kondisi ?? 'baru') : $kondisi;
                    $selisih     = $qtyBaru - $qtyLama;

                    if ($kondisiLama !== $kondisi && $detailLama) {
                        $inv->tambahStok($qtyLama, $kondisiLama, true);
                        $inv->kurangiStok($qtyBaru, $kondisi, true);
                    } elseif ($selisih > 0) {
                        $inv->kurangiStok($selisih, $kondisi, true);
                    } elseif ($selisih < 0) {
                        $inv->tambahStok(abs($selisih), $kondisi, true);
                    }
                }
            }

            if ($detailLama) {
                DetailPenyewaan::where('id', $detailId)->update($data);
                $submittedIds[] = $detailId;
            } else {
                $created        = DetailPenyewaan::create($data);
                $submittedIds[] = $created->id;
            }

            $totalSewa += $subtotal;
        }

        $toDelete = array_diff($existingIds, $submittedIds);
        if (!empty($toDelete)) {
            $detailsToDelete = DetailPenyewaan::whereIn('id', $toDelete)->get();
            foreach ($detailsToDelete as $detail) {
                if ($detail->inventory_id) {
                    $inv = Inventory::find($detail->inventory_id);
                    if ($inv) {
                        $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru', true);
                    }
                }
            }
            DetailPenyewaan::whereIn('id', $toDelete)->delete();
        }

        $penyewaan->update(['total_harga_sewa' => $totalSewa]);
    }

    // =========================================================
    //  PRIVATE HELPER — hitung durasi dari tanggal (server-side)
    // =========================================================

    private function hitungDurasi(string $tglMulai, string $tglSelesai): int
    {
        $start = Carbon::parse($tglMulai)->startOfDay();
        $end   = Carbon::parse($tglSelesai)->startOfDay();
        return (int) $start->diffInDays($end);
    }

    // =========================================================
    //  PRIVATE HELPER — validasi stok sebelum store/update
    // =========================================================

    private function validateStok(array $items, ?int $excludePenyewaanId = null): array
    {
        $errors    = [];
        $requested = [];

        foreach ($items as $item) {
            $invId   = !empty($item['inventory_id']) ? (int) $item['inventory_id'] : null;
            $kondisi = in_array($item['kondisi'] ?? '', ['baru', 'bekas']) ? $item['kondisi'] : 'baru';
            $qty     = max(1, (int) ($item['qty'] ?? 1));
            if (!$invId) continue;
            $key = $invId . '_' . $kondisi;
            $requested[$key] = ($requested[$key] ?? 0) + $qty;
        }

        foreach ($requested as $key => $totalQtyDiminta) {
            [$invId, $kondisi] = explode('_', $key, 2);
            $inv = Inventory::find((int) $invId);
            if (!$inv) continue;

            $qtyDipakai = 0;
            if ($excludePenyewaanId) {
                $qtyDipakai = DetailPenyewaan::where('inventory_id', $invId)
                    ->where('kondisi', $kondisi)
                    ->whereHas('penyewaan', function ($q) use ($excludePenyewaanId) {
                        $q->whereIn('status', ['berjalan', 'segera_konfirmasi'])
                          ->where('id', '!=', $excludePenyewaanId);
                    })
                    ->sum('qty');
            } else {
                $qtyDipakai = DetailPenyewaan::where('inventory_id', $invId)
                    ->where('kondisi', $kondisi)
                    ->whereHas('penyewaan', function ($q) {
                        $q->whereIn('status', ['berjalan', 'segera_konfirmasi']);
                    })
                    ->sum('qty');
            }

            $stokField  = $kondisi === 'baru' ? 'stok_baru' : 'stok_bekas';
            $stokTotal  = (int) ($inv->$stokField ?? 0);
            $stokAktual = max(0, $stokTotal - $qtyDipakai);

            if ($totalQtyDiminta > $stokAktual) {
                $errors[] = "Stok {$kondisi} \"{$inv->nama_produk}\" tidak cukup. "
                          . "Diminta: {$totalQtyDiminta}, tersedia: {$stokAktual}.";
            }
        }

        return $errors;
    }

    // =========================================================
    //  INDEX
    // =========================================================

    public function index(Request $request)
    {
        $search   = $request->input('search', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo   = $request->input('date_to', '');
        $status   = $request->input('status', '');
        $perPage  = in_array($request->input('per_page'), [5, 10, 25, 50])
                    ? (int) $request->input('per_page')
                    : 10;

        $aktif = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])->get();
        foreach ($aktif as $item) {
            $this->syncStatus($item);
        }

        $penyewaans = Penyewaan::with('details')
            ->when($search, function ($q) use ($search) {
                $q->where('nama_penyewa',       'like', "%{$search}%")
                  ->orWhere('nomor_telepon',     'like', "%{$search}%")
                  ->orWhere('produk_alkes',      'like', "%{$search}%")
                  ->orWhere('status',            'like', "%{$search}%")
                  ->orWhere('pengiriman',        'like', "%{$search}%")
                  ->orWhere('alamat_penyewa',    'like', "%{$search}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan',        'like', "%{$search}%")
                  ->orWhere('tgl_mulai',         'like', "%{$search}%")
                  ->orWhere('tgl_selesai',       'like', "%{$search}%");
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('tgl_mulai', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('tgl_mulai', '<=', $dateTo);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $statusCounts = [
            'semua'             => Penyewaan::count(),
            'berjalan'          => Penyewaan::where('status', 'berjalan')->count(),
            'segera_konfirmasi' => Penyewaan::where('status', 'segera_konfirmasi')->count(),
            'selesai'           => Penyewaan::where('status', 'selesai')->count(),
            'dibatalkan'        => Penyewaan::where('status', 'dibatalkan')->count(),
        ];

        return view('admin.penyewaan.index', compact(
            'penyewaans', 'search', 'perPage', 'dateFrom', 'dateTo', 'status', 'statusCounts'
        ));
    }

    // =========================================================
    //  SHOW
    // =========================================================

    public function show(string $id)
    {
        $penyewaan = Penyewaan::with([
            'details.inventory',
            'extends',
        ])->findOrFail($id);

        $this->syncStatus($penyewaan);

        // Cari ID extend terbaru yang masih aktif (untuk keperluan UI)
        $latestActiveExtendId = $penyewaan->extends
            ->where('status_extend', 'aktif')
            ->sortByDesc('id')
            ->first()?->id;

        return view('admin.penyewaan.show', compact('penyewaan', 'latestActiveExtendId'));
    }

    // =========================================================
    //  CREATE & STORE
    // =========================================================

    public function create()
    {
        $inventories = Inventory::orderBy('nama_produk')->get();
        return view('admin.penyewaan.create', compact('inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penyewa'        => 'required|string|max:255',
            'nomor_telepon'       => 'required|string|max:20',
            'tempat_tanggal_lahir'=> 'nullable|string|max:255',
            'nomor_ktp'           => 'nullable|string|max:30',
            'alamat_penyewa'      => 'required|string|max:500',
            'tgl_mulai'           => 'required|date',
            'tgl_selesai'         => 'required|date|after_or_equal:tgl_mulai',
            'pengiriman'          => 'required|string',
            'biaya_ongkir'        => 'nullable|integer|min:0',
            'diskon_global'       => 'nullable|integer|min:0',
            'metode_pembayaran'   => 'required|string|max:100',
            'bukti_pembayaran'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'foto_ktp_sim'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'keterangan'          => 'nullable|string|max:1000',
            'items'               => 'required|array|min:1',
            'items.*.nama_alat'   => 'required|string|max:255',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.harga_satuan'=> 'required|integer|min:0',
        ]);

        $stokErrors = $this->validateStok($request->items);
        if (!empty($stokErrors)) {
            return back()->withInput()->withErrors(['stok' => $stokErrors]);
        }

        $tglMulai   = Carbon::parse($request->tgl_mulai)->startOfDay();
        $tglSelesai = Carbon::parse($request->tgl_selesai)->startOfDay();
        $durasiHari = (int) $tglMulai->diffInDays($tglSelesai);
        $sisaHari   = (int) Carbon::today()->startOfDay()->diffInDays($tglSelesai, false);
        $status     = $sisaHari > 7 ? 'berjalan' : 'segera_konfirmasi';

        $buktiPath = $request->hasFile('bukti_pembayaran')
                     ? $request->file('bukti_pembayaran')->store('penyewaan/bukti', 'public')
                     : null;
        $ktpPath   = $request->hasFile('foto_ktp_sim')
                     ? $request->file('foto_ktp_sim')->store('penyewaan/ktp', 'public')
                     : null;

        $penyewaan = Penyewaan::create([
            'nama_penyewa'         => $request->nama_penyewa,
            'nomor_telepon'        => $request->nomor_telepon,
            'tempat_tanggal_lahir' => $request->tempat_tanggal_lahir,
            'nomor_ktp'            => $request->nomor_ktp,
            'alamat_penyewa'       => $request->alamat_penyewa,
            'produk_alkes'         => null,
            'tgl_mulai'            => $tglMulai->format('Y-m-d'),
            'tgl_selesai'          => $tglSelesai->format('Y-m-d'),
            'durasi_hari'          => $durasiHari,
            'pengiriman'           => $request->pengiriman,
            'biaya_ongkir'         => (int) ($request->biaya_ongkir ?? 0),
            'diskon_global'        => (int) ($request->diskon_global ?? 0),
            'total_harga_sewa'     => 0,
            'metode_pembayaran'    => $request->metode_pembayaran,
            'bukti_pembayaran'     => $buktiPath,
            'foto_ktp_sim'         => $ktpPath,
            'status'               => $status,
            'keterangan'           => $request->keterangan,
        ]);

        $this->syncDetails($penyewaan, $request->items);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'create',
            subject:  $penyewaan->nama_penyewa,
            oldValue:  [],
            newValue: [
                'id'          => $penyewaan->id,
                'tgl_mulai'   => $tglMulai->format('d M Y'),
                'tgl_selesai' => $tglSelesai->format('d M Y'),
                'durasi'      => $durasiHari . ' hari',
                'status'      => $status,
            ],
            pageUrl: 'penyewaan/' . $penyewaan->id
        );

        return redirect()->route('penyewaan.index')
                         ->with('success', 'Data penyewaan berhasil disimpan.');
    }

    // =========================================================
    //  EDIT & UPDATE
    // =========================================================

    public function edit(string $id)
    {
        $penyewaan   = Penyewaan::with('details.inventory')->findOrFail($id);
        $inventories = Inventory::orderBy('nama_produk')->get();
        return view('admin.penyewaan.edit', compact('penyewaan', 'inventories'));
    }

    public function update(Request $request, string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);

        $request->validate([
            'nama_penyewa'        => 'required|string|max:255',
            'nomor_telepon'       => 'required|string|max:20',
            'tempat_tanggal_lahir'=> 'nullable|string|max:255',
            'nomor_ktp'           => 'nullable|string|max:30',
            'alamat_penyewa'      => 'required|string|max:500',
            'tgl_mulai'           => 'required|date',
            'tgl_selesai'         => 'required|date|after_or_equal:tgl_mulai',
            'pengiriman'          => 'required|string',
            'biaya_ongkir'        => 'nullable|integer|min:0',
            'diskon_global'       => 'nullable|integer|min:0',
            'metode_pembayaran'   => 'required|string|max:100',
            'bukti_pembayaran'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'foto_ktp_sim'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'keterangan'          => 'nullable|string|max:1000',
            'items'               => 'required|array|min:1',
            'items.*.nama_alat'   => 'required|string|max:255',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.harga_satuan'=> 'required|integer|min:0',
        ]);

        $stokErrors = $this->validateStok($request->items, (int) $id);
        if (!empty($stokErrors)) {
            return back()->withInput()->withErrors(['stok' => $stokErrors]);
        }

        $tglMulai   = Carbon::parse($request->tgl_mulai)->startOfDay();
        $tglSelesai = Carbon::parse($request->tgl_selesai)->startOfDay();
        $durasiHari = (int) $tglMulai->diffInDays($tglSelesai);
        $sisaHari   = (int) Carbon::today()->startOfDay()->diffInDays($tglSelesai, false);

        if (! in_array($penyewaan->status, ['selesai', 'dibatalkan'])) {
            $status = $sisaHari > 7 ? 'berjalan' : 'segera_konfirmasi';
        } else {
            $status = $penyewaan->status;
        }

        $buktiPath = $penyewaan->bukti_pembayaran;
        if ($request->hasFile('bukti_pembayaran')) {
            if ($buktiPath) \Storage::disk('public')->delete($buktiPath);
            $buktiPath = $request->file('bukti_pembayaran')->store('penyewaan/bukti', 'public');
        }

        $ktpPath = $penyewaan->foto_ktp_sim;
        if ($request->hasFile('foto_ktp_sim')) {
            if ($ktpPath) \Storage::disk('public')->delete($ktpPath);
            $ktpPath = $request->file('foto_ktp_sim')->store('penyewaan/ktp', 'public');
        }

        $oldValues = [
            'nama_penyewa' => $penyewaan->nama_penyewa,
            'tgl_mulai'    => $penyewaan->tgl_mulai?->format('d M Y'),
            'tgl_selesai'  => $penyewaan->tgl_selesai?->format('d M Y'),
            'status'       => $penyewaan->status,
        ];

        $penyewaan->update([
            'nama_penyewa'         => $request->nama_penyewa,
            'nomor_telepon'        => $request->nomor_telepon,
            'tempat_tanggal_lahir' => $request->tempat_tanggal_lahir,
            'nomor_ktp'            => $request->nomor_ktp,
            'alamat_penyewa'       => $request->alamat_penyewa,
            'tgl_mulai'            => $tglMulai->format('Y-m-d'),
            'tgl_selesai'          => $tglSelesai->format('Y-m-d'),
            'durasi_hari'          => $durasiHari,
            'pengiriman'           => $request->pengiriman,
            'biaya_ongkir'         => (int) ($request->biaya_ongkir ?? 0),
            'diskon_global'        => (int) ($request->diskon_global ?? 0),
            'metode_pembayaran'    => $request->metode_pembayaran,
            'bukti_pembayaran'     => $buktiPath,
            'foto_ktp_sim'         => $ktpPath,
            'status'               => $status,
            'keterangan'           => $request->keterangan,
        ]);

        $this->syncDetails($penyewaan, $request->items);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'update',
            subject:  $penyewaan->nama_penyewa,
            oldValue: $oldValues,
            newValue: [
                'tgl_mulai'   => $tglMulai->format('d M Y'),
                'tgl_selesai' => $tglSelesai->format('d M Y'),
                'durasi'      => $durasiHari . ' hari',
                'status'      => $status,
            ],
            pageUrl: 'penyewaan/' . $penyewaan->id . '/edit'
        );

        return redirect()->route('penyewaan.index')
                         ->with('success', 'Data penyewaan berhasil diperbarui.');
    }

    // =========================================================
    //  DESTROY
    // =========================================================

    public function destroy(string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);

        if ($penyewaan->bukti_pembayaran) {
            \Storage::disk('public')->delete($penyewaan->bukti_pembayaran);
        }
        if ($penyewaan->foto_ktp_sim) {
            \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
        }

        if (in_array($penyewaan->status, ['berjalan', 'segera_konfirmasi'])) {
            foreach ($penyewaan->details as $detail) {
                if ($detail->inventory_id) {
                    $inv = Inventory::find($detail->inventory_id);
                    if ($inv) $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru', true);
                }
            }
        }

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'delete',
            subject:  $penyewaan->nama_penyewa,
            oldValue: ['status' => $penyewaan->status],
            newValue: [],
            pageUrl:  'penyewaan'
        );

        $penyewaan->delete();

        return redirect()->route('penyewaan.index')
                         ->with('success', 'Data penyewaan berhasil dihapus.');
    }

    // =========================================================
    //  BATALKAN PENYEWAAN
    // =========================================================

    public function batalkan(Request $request, string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);

        if ($penyewaan->status === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Penyewaan ini sudah berstatus dibatalkan.',
            ], 422);
        }

        $request->validate([
            'alasan_batal' => 'required|string|max:1000',
        ]);

        $statusLama = $penyewaan->status;

        $penyewaan->update([
            'status'        => 'dibatalkan',
            'alasan_batal'  => $request->alasan_batal,
            'dibatalkan_at' => now(),
        ]);

        if (in_array($statusLama, ['berjalan', 'segera_konfirmasi'])) {
            $this->kembalikanStok($penyewaan);
        }

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'update',
            subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
            oldValue: ['status' => $statusLama],
            newValue: [
                'status'        => 'dibatalkan',
                'alasan_batal'  => $request->alasan_batal,
                'dibatalkan_at' => now()->format('d M Y H:i'),
            ],
            pageUrl: 'penyewaan/' . $penyewaan->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Penyewaan berhasil dibatalkan.',
        ]);
    }

    // =========================================================
    //  RESTORE (PULIHKAN) PENYEWAAN
    // =========================================================

    public function restore(Request $request, string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);

        if ($penyewaan->status !== 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Penyewaan ini tidak berstatus dibatalkan.',
            ], 422);
        }

        $sisaHari  = $penyewaan->sisa_hari;
        $newStatus = $sisaHari > 7 ? 'berjalan' : 'segera_konfirmasi';

        $penyewaan->update([
            'status'        => $newStatus,
            'alasan_batal'  => null,
            'dibatalkan_at' => null,
        ]);

        $this->kurangiStokPenyewaan($penyewaan);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'update',
            subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
            oldValue: ['status' => 'dibatalkan'],
            newValue: [
                'status'     => $newStatus,
                'dipulihkan' => now()->format('d M Y H:i'),
            ],
            pageUrl: 'penyewaan/' . $penyewaan->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Penyewaan berhasil dipulihkan dengan status ' . $newStatus . '.',
        ]);
    }

    // =========================================================
    //  EXPORT XLSX
    // =========================================================

    public function export(Request $request)
    {
        $search   = $request->get('search',   '');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $status   = $request->get('status', 'semua');

        $filename = 'penyewaan_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new PenyewaanExport($search, $dateFrom, $dateTo, $status),
            $filename
        );
    }

    // =========================================================
    //  MONITORING & NOTIFIKASI
    // =========================================================

    public function monitoring()
    {
        $aktif = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])->get();
        foreach ($aktif as $item) {
            $this->syncStatus($item);
        }

        $data = Penyewaan::with('details')
            ->whereIn('status', ['berjalan', 'segera_konfirmasi'])
            ->orderBy('tgl_selesai', 'asc')
            ->get()
            ->map(function ($item) {
                $barang = $item->details->count()
                    ? $item->details->pluck('nama_alat')->implode(', ')
                    : ($item->produk_alkes ?? '-');

                return [
                    'id'              => $item->id,
                    'nama'            => $item->nama_penyewa,
                    'nomor_hp'        => $item->nomor_telepon,
                    'barang'          => $barang,
                    'alamat'          => $item->alamat_penyewa,
                    'durasi_hari'     => $item->durasi_hari,
                    'sisa_hari'       => $item->sisa_hari,
                    'tgl_selesai'     => $item->tgl_selesai?->format('d M Y') ?? '-',
                    'tgl_selesai_raw' => $item->tgl_selesai?->format('Y-m-d') ?? null,
                    'status'          => $item->status,
                    'status_label'    => $item->status_label,
                    'status_class'    => $item->status_class,
                ];
            });

        return response()->json($data);
    }

    public function notifikasi()
    {
        $aktif = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])->get();
        foreach ($aktif as $item) {
            $this->syncStatus($item);
        }

        $data = Penyewaan::with('details')
            ->where('status', 'segera_konfirmasi')
            ->orderBy('tgl_selesai', 'asc')
            ->get()
            ->map(function ($item) {
                $sisaHari = $item->sisa_hari;

                $sisaLabel = match (true) {
                    $sisaHari <= 0  => 'Lewat deadline!',
                    $sisaHari === 1 => 'Besok deadline!',
                    $sisaHari === 2 => '2 hari lagi',
                    default         => $sisaHari . ' hari lagi',
                };

                $barang = $item->details->count()
                    ? $item->details->pluck('nama_alat')->implode(', ')
                    : ($item->produk_alkes ?? '-');

                return [
                    'id'              => $item->id,
                    'nama'            => $item->nama_penyewa,
                    'barang'          => $barang,
                    'durasi_hari'     => $item->durasi_hari,
                    'sisa_hari'       => $sisaHari,
                    'sisa_label'      => $sisaLabel,
                    'tgl_selesai'     => $item->tgl_selesai?->format('d M Y') ?? '-',
                    'tgl_selesai_raw' => $item->tgl_selesai?->format('Y-m-d') ?? null,
                ];
            });

        return response()->json([
            'count' => $data->count(),
            'items' => $data,
        ]);
    }

    // =========================================================
    //  SELESAIKAN
    // =========================================================

    public function selesaikan(Request $request, string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);
        $action    = $request->input('action', 'selesai_sekarang');

        if ($action === 'selesai_sekarang') {
            $oldStatus = $penyewaan->status;

            $penyewaan->update(['status' => 'selesai']);
            $this->kembalikanStok($penyewaan);

            ActivityLog::record(
                module:   'Penyewaan',
                action:   'update',
                subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
                oldValue: ['status' => $oldStatus],
                newValue: [
                    'status'       => 'selesai',
                    'diselesaikan' => Carbon::today()->format('d M Y'),
                ],
                pageUrl: 'penyewaan/' . $penyewaan->id . '/selesaikan'
            );

            return response()->json([
                'success' => true,
                'action'  => 'selesai_sekarang',
                'message' => 'Penyewaan berhasil diselesaikan dan stok barang telah dikembalikan sebagai bekas.',
            ]);
        }

        if ($action === 'sesuai_deadline') {
            return response()->json([
                'success' => true,
                'action'  => 'sesuai_deadline',
                'message' => 'Penyewaan akan otomatis selesai saat deadline tiba.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Action tidak dikenali.'], 422);
    }

    // =========================================================
    //  EXTEND — method lama (backward compat)
    // =========================================================

    public function extend(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        $request->validate([
            'tgl_selesai_baru' => [
                'required',
                'date',
                'after:' . $penyewaan->tgl_selesai->format('Y-m-d'),
            ],
        ]);

        $tglLama  = $penyewaan->tgl_selesai->format('d M Y');
        $tglBaru  = Carbon::parse($request->tgl_selesai_baru)->startOfDay();
        $tglMulai = Carbon::parse($penyewaan->tgl_mulai->format('Y-m-d'))->startOfDay();

        $durasiHari = (int) $tglMulai->diffInDays($tglBaru);
        $sisaBaru   = (int) Carbon::today()->startOfDay()->diffInDays($tglBaru, false);
        $newStatus  = $sisaBaru > 7 ? 'berjalan' : 'segera_konfirmasi';

        $penyewaan->update([
            'tgl_selesai' => $tglBaru->format('Y-m-d'),
            'durasi_hari' => $durasiHari,
            'status'      => $newStatus,
        ]);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'update',
            subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
            oldValue: ['tgl_selesai' => $tglLama],
            newValue: [
                'tgl_selesai' => $tglBaru->format('d M Y'),
                'durasi'      => $durasiHari . ' hari',
            ],
            pageUrl: 'penyewaan/' . $penyewaan->id . '/extend'
        );

        return response()->json([
            'success' => true,
            'message' => 'Deadline berhasil di-extend ke ' . $tglBaru->format('d M Y') . '.',
        ]);
    }

    // =========================================================
    //  EXTEND STORE
    // =========================================================

    public function extendStore(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        $tglSelesaiSekarang = $penyewaan->tgl_selesai->format('Y-m-d');

        $request->validate([
            'tgl_selesai_baru' => [
                'required', 'date',
                'after:' . $tglSelesaiSekarang,
            ],
            'harga_extend'   => 'required|integer|min:0',
            'metode_bayar'   => 'required|string|max:100',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan'        => 'nullable|string|max:1000',
        ], [
            'tgl_selesai_baru.required' => 'Tanggal extend wajib diisi.',
            'tgl_selesai_baru.after'    => 'Tanggal extend harus setelah deadline saat ini (' . $penyewaan->tgl_selesai->format('d M Y') . ').',
            'harga_extend.required'     => 'Harga extend wajib diisi.',
            'harga_extend.min'          => 'Harga extend tidak boleh negatif.',
            'metode_bayar.required'     => 'Metode pembayaran wajib dipilih.',
            'bukti_transfer.mimes'      => 'Bukti transfer harus berupa file JPG, PNG, atau PDF.',
            'bukti_transfer.max'        => 'Ukuran file bukti transfer maksimal 5 MB.',
        ]);

        $tglSelesaiLamaRaw = $tglSelesaiSekarang;
        $tglLamaLabel      = $penyewaan->tgl_selesai->format('d M Y');

        $tglBaru  = Carbon::parse($request->tgl_selesai_baru)->startOfDay();
        $tglMulai = Carbon::parse($penyewaan->tgl_mulai->format('Y-m-d'))->startOfDay();

        $durasiHari = (int) $tglMulai->diffInDays($tglBaru);
        $tambahHari = (int) Carbon::parse($tglSelesaiLamaRaw)->startOfDay()->diffInDays($tglBaru);
        $sisaBaru   = (int) Carbon::today()->startOfDay()->diffInDays($tglBaru, false);
        $newStatus  = $sisaBaru > 7 ? 'berjalan' : 'segera_konfirmasi';

        $pathBukti = null;
        if ($request->hasFile('bukti_transfer')) {
            $pathBukti = $request->file('bukti_transfer')
                ->store('penyewaan/extend', 'public');
        }

        $penyewaan->update([
            'tgl_selesai' => $tglBaru->format('Y-m-d'),
            'durasi_hari' => $durasiHari,
            'status'      => $newStatus,
        ]);

        $extend = PenyewaanExtend::create([
            'penyewaan_id'     => $penyewaan->id,
            'tgl_selesai_lama' => $tglSelesaiLamaRaw,
            'tgl_selesai_baru' => $tglBaru->format('Y-m-d'),
            'tambah_hari'      => $tambahHari,
            'harga_extend'     => (int) $request->harga_extend,
            'metode_bayar'     => $request->metode_bayar,
            'bukti_transfer'   => $pathBukti,
            'catatan'          => $request->catatan ?? null,
            'status_extend'    => 'aktif',   // ← eksplisit default
        ]);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'update',
            subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
            oldValue: ['tgl_selesai' => $tglLamaLabel],
            newValue: [
                'tgl_selesai'  => $tglBaru->format('d M Y'),
                'tambah_hari'  => $tambahHari . ' hari',
                'harga_extend' => 'Rp ' . number_format($request->harga_extend, 0, ',', '.'),
                'metode_bayar' => $request->metode_bayar,
                'catatan'      => $request->catatan ?? '-',
            ],
            pageUrl: 'penyewaan/' . $penyewaan->id . '/extend-store'
        );

        return response()->json([
            'success'     => true,
            'message'     => 'Deadline berhasil di-extend ke ' . $tglBaru->format('d M Y') . '.',
            'extend_id'   => $extend->id,
            'tgl_baru'    => $tglBaru->format('d M Y'),
            'tambah_hari' => $tambahHari,
            'durasi'      => $durasiHari,
            'path_bukti'  => $pathBukti,
        ]);
    }

    // =========================================================
    //  BATALKAN EXTEND ← BARU
    // =========================================================

    public function batalkanExtend(Request $request, string $extendId)
    {
        $extend    = PenyewaanExtend::with('penyewaan')->findOrFail($extendId);
        $penyewaan = $extend->penyewaan;

        // ── Guard 1: extend sudah dibatalkan ──
        if ($extend->status_extend === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Extend ini sudah dibatalkan sebelumnya.',
            ], 422);
        }

        // ── Guard 2: penyewaan sudah selesai/dibatalkan ──
        if (in_array($penyewaan->status, ['selesai', 'dibatalkan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat membatalkan extend karena penyewaan sudah ' . $penyewaan->status . '.',
            ], 422);
        }

        // ── Guard 3: hanya extend TERBARU yang aktif yang bisa dibatalkan ──
        $latestActiveExtend = PenyewaanExtend::where('penyewaan_id', $penyewaan->id)
            ->where('status_extend', 'aktif')
            ->orderByDesc('id')
            ->first();

        if (!$latestActiveExtend || $latestActiveExtend->id !== $extend->id) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya extend terbaru yang aktif yang dapat dibatalkan. '
                           . 'Batalkan extend yang lebih baru terlebih dahulu.',
            ], 422);
        }

        $request->validate([
            'alasan_batal_extend' => 'nullable|string|max:1000',
        ]);

        // ── Rollback tgl_selesai penyewaan ke tgl_selesai_lama extend ini ──
        $tglRollback = Carbon::parse($extend->tgl_selesai_lama->format('Y-m-d'))->startOfDay();
        $tglMulai    = Carbon::parse($penyewaan->tgl_mulai->format('Y-m-d'))->startOfDay();
        $durasiHari  = (int) $tglMulai->diffInDays($tglRollback);
        $sisaHari    = (int) Carbon::today()->startOfDay()->diffInDays($tglRollback, false);
        $newStatus   = $sisaHari > 7 ? 'berjalan' : 'segera_konfirmasi';

        // ── Update extend: tandai dibatalkan ──
        $extend->update([
            'status_extend'        => 'dibatalkan',
            'alasan_batal_extend'  => $request->alasan_batal_extend ?: null,
            'dibatalkan_extend_at' => now(),
        ]);

        // ── Update penyewaan: rollback tgl_selesai & durasi ──
        $penyewaan->update([
            'tgl_selesai' => $tglRollback->format('Y-m-d'),
            'durasi_hari' => $durasiHari,
            'status'      => $newStatus,
        ]);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'update',
            subject:  'Batalkan Extend #' . $extend->id . ' — ' . $penyewaan->nama_penyewa,
            oldValue: [
                'tgl_selesai'    => $extend->tgl_selesai_baru->format('d M Y'),
                'status_extend'  => 'aktif',
            ],
            newValue: [
                'tgl_selesai'         => $tglRollback->format('d M Y'),
                'status_extend'       => 'dibatalkan',
                'alasan_batal_extend' => $request->alasan_batal_extend ?: '-',
                'dibatalkan_at'       => now()->format('d M Y H:i'),
            ],
            pageUrl: 'penyewaan/' . $penyewaan->id
        );

        return response()->json([
            'success'           => true,
            'message'           => 'Extend berhasil dibatalkan. Deadline dikembalikan ke '
                                 . $tglRollback->format('d M Y') . '.',
            'tgl_selesai_baru'  => $tglRollback->format('d M Y'),
            'durasi_hari'       => $durasiHari,
            'status'            => $newStatus,
        ]);
    }

    // =========================================================
    //  CETAK DOKUMEN
    // =========================================================

    public function invoiceExtend(string $extendId)
    {
        $extend    = PenyewaanExtend::with('penyewaan.details')->findOrFail($extendId);
        $penyewaan = $extend->penyewaan;
        return view('admin.penyewaan.cetak.invoice_extend', compact('extend', 'penyewaan'));
    }

    public function perjanjianExtend(string $extendId)
    {
        $extend    = PenyewaanExtend::with('penyewaan.details')->findOrFail($extendId);
        $penyewaan = $extend->penyewaan;
        return view('admin.penyewaan.cetak.perjanjian_extend', compact('extend', 'penyewaan'));
    }

    public function invoice(string $id)
    {
        $penyewaan = Penyewaan::with('details.inventory')->findOrFail($id);
        return view('admin.penyewaan.cetak.invoice', compact('penyewaan'));
    }

    public function perjanjian(string $id)
    {
        $penyewaan = Penyewaan::with('details.inventory')->findOrFail($id);
        return view('admin.penyewaan.cetak.perjanjian', compact('penyewaan'));
    }
}