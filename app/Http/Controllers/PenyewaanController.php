<?php
// app/Http/Controllers/PenyewaanController.php

namespace App\Http\Controllers;

use App\Exports\PenyewaanExport;
use App\Models\ActivityLog;
use App\Models\DetailPenyewaan;
use App\Models\Inventory;
use App\Models\Penyewaan;
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
        if ($item->status === 'selesai' || $item->status === 'dibatalkan') return;

        $sisaHari = $item->sisa_hari;

        if ($sisaHari <= 0) {
            // FIX: Hanya update status, JANGAN update tgl_selesai.
            // Mengubah tgl_selesai akan membuat durasi_hari (kolom DB) di-override
            // saat extend, sehingga kolom durasi di tabel utama ikut berubah.
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
                /** @var Inventory $inv */
                $inv = Inventory::find($inventoryId);
                if ($inv) {
                    $qtyLama      = $detailLama ? (int) $detailLama->qty : 0;
                    $kondisiLama  = $detailLama ? ($detailLama->kondisi ?? 'baru') : $kondisi;
                    $selisih      = $qtyBaru - $qtyLama;

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
    //  Dipakai saat store() dan update() untuk menyimpan nilai awal.
    // =========================================================

    private function hitungDurasi(string $tglMulai, string $tglSelesai): int
    {
        $start = Carbon::parse($tglMulai)->startOfDay();
        $end   = Carbon::parse($tglSelesai)->startOfDay();
        return (int) $start->diffInDays($end);
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
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penyewaan.index', compact(
            'penyewaans', 'search', 'perPage', 'dateFrom', 'dateTo'
        ));
    }

    // =========================================================
    //  EXPORT XLSX
    // =========================================================

    public function export(Request $request)
    {
        $search   = $request->get('search',   '');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $status   = $request->get('status',   'semua');

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
                    // durasi_hari = nilai tetap dari kolom DB (tidak berubah seiring hari)
                    'durasi_hari'     => $item->durasi_hari,
                    // sisa_hari = dinamis, untuk warna & logika selesaikan
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
                    // durasi_hari = tetap (info konteks berapa lama sewa)
                    'durasi_hari'     => $item->durasi_hari,
                    // sisa_hari = dinamis (countdown ke deadline)
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
    //  SELESAIKAN & EXTEND
    // =========================================================

    public function selesaikan(Request $request, string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);
        $action    = $request->input('action', 'selesai_sekarang');

        if ($action === 'selesai_sekarang') {
            $oldStatus = $penyewaan->status;

            // FIX: JANGAN update tgl_selesai ke Carbon::today().
            // Mengubah tgl_selesai akan menyebabkan kolom durasi_hari di tabel utama
            // ikut berubah (karena accessor sebelumnya menghitung dari tgl_mulai→tgl_selesai).
            // Cukup update status saja — tgl_selesai tetap sesuai kontrak awal.
            $penyewaan->update([
                'status' => 'selesai',
            ]);

            $this->kembalikanStok($penyewaan);

            ActivityLog::record(
                module:   'Penyewaan',
                action:   'update',
                subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
                oldValue: ['status' => $oldStatus],
                newValue: [
                    'status'      => 'selesai',
                    'diselesaikan' => Carbon::today()->format('d M Y'),
                ],
                pageUrl:  'penyewaan/' . $penyewaan->id . '/selesaikan'
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

        $tglLama    = $penyewaan->tgl_selesai->format('d M Y');
        $tglBaru    = Carbon::parse($request->tgl_selesai_baru)->startOfDay();
        $tglMulai   = Carbon::parse($penyewaan->tgl_mulai->format('Y-m-d'))->startOfDay();

        // FIX: durasi_hari saat extend dihitung dari tgl_mulai → tgl_selesai BARU.
        // Ini benar karena extend memang memperpanjang kontrak, bukan sekedar status.
        // Nilai ini disimpan ke kolom DB sehingga tabel utama menampilkan durasi extend.
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
    //  CREATE & STORE
    // =========================================================

    public function create()
    {
        return view('admin.penyewaan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penyewa'             => 'required|string|max:255',
            'nomor_telepon'            => 'required|string|max:20',
            'tempat_tanggal_lahir'     => 'nullable|string|max:255',
            'nomor_ktp'                => ['nullable', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'alamat_penyewa'           => 'required|string',
            'tgl_mulai'                => 'required|date',
            'tgl_selesai'              => 'required|date|after_or_equal:tgl_mulai',
            'pengiriman'               => 'required|in:mandiri,Gosend / GrabExpress,Rental Mobil Paralkes',
            'biaya_ongkir'             => 'nullable|integer|min:0',
            'diskon_global'            => 'nullable|integer|min:0',
            'metode_pembayaran'        => 'required|in:tunai,transfer,qris',
            'bukti_pembayaran'         => 'nullable|string|max:500',
            'foto_ktp_sim'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'keterangan'               => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.inventory_id'     => 'nullable|integer',
            'items.*.kondisi'          => 'nullable|in:baru,bekas',
            'items.*.nama_alat'        => 'required|string|max:255',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.satuan'           => 'required|string|max:50',
            'items.*.harga_satuan'     => 'required|integer|min:0',
            'items.*.diskon'           => 'nullable|integer|min:0|max:100',
        ], [
            'nomor_ktp.size'  => 'Nomor KTP harus tepat 16 digit.',
            'nomor_ktp.regex' => 'Nomor KTP hanya boleh berisi angka.',
        ]);

        // Hitung durasi server-side dan simpan ke kolom DB sebagai nilai tetap
        $validated['durasi_hari'] = $this->hitungDurasi(
            $validated['tgl_mulai'],
            $validated['tgl_selesai']
        );

        if ($request->hasFile('foto_ktp_sim')) {
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        }

        $validated['biaya_ongkir']     = $validated['biaya_ongkir']  ?? 0;
        $validated['diskon_global']    = $validated['diskon_global'] ?? 0;
        $validated['total_harga_sewa'] = 0;
        $validated['status']           = 'berjalan';

        $penyewaan = Penyewaan::create(collect($validated)->except('items')->toArray());

        $this->syncDetails($penyewaan, $validated['items']);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'create',
            subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
            newValue: [
                'penyewa'     => $penyewaan->nama_penyewa,
                'alat'        => collect($validated['items'])->pluck('nama_alat')->implode(', '),
                'tgl_mulai'   => $penyewaan->tgl_mulai->format('d M Y'),
                'tgl_selesai' => $penyewaan->tgl_selesai->format('d M Y'),
                'total'       => 'Rp ' . number_format($penyewaan->total_tagihan, 0, ',', '.'),
            ],
            pageUrl: 'penyewaan'
        );

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil ditambahkan.');
    }

    // =========================================================
    //  SHOW
    // =========================================================

    public function show(string $id)
    {
        $penyewaan = Penyewaan::with('details.inventory')->findOrFail($id);
        return view('admin.penyewaan.show', compact('penyewaan'));
    }

    // =========================================================
    //  EDIT & UPDATE
    // =========================================================

    public function edit(string $id)
    {
        $penyewaan = Penyewaan::with('details')->findOrFail($id);
        return view('admin.penyewaan.edit', compact('penyewaan'));
    }

    public function update(Request $request, string $id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        $oldData = [
            'penyewa'     => $penyewaan->nama_penyewa,
            'status'      => $penyewaan->status,
            'tgl_selesai' => $penyewaan->tgl_selesai?->format('d M Y'),
            'total'       => 'Rp ' . number_format($penyewaan->total_tagihan, 0, ',', '.'),
        ];

        $validated = $request->validate([
            'nama_penyewa'             => 'required|string|max:255',
            'nomor_telepon'            => 'required|string|max:20',
            'tempat_tanggal_lahir'     => 'nullable|string|max:255',
            'nomor_ktp'                => ['nullable', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'alamat_penyewa'           => 'required|string',
            'tgl_mulai'                => 'required|date',
            'tgl_selesai'              => 'required|date|after_or_equal:tgl_mulai',
            'pengiriman'               => 'required|in:mandiri,Gosend / GrabExpress,Rental Mobil Paralkes',
            'biaya_ongkir'             => 'nullable|integer|min:0',
            'diskon_global'            => 'nullable|integer|min:0',
            'metode_pembayaran'        => 'required|in:tunai,transfer,qris',
            'bukti_pembayaran'         => 'nullable|string|max:500',
            'foto_ktp_sim'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status'                   => 'required|in:berjalan,segera_konfirmasi,selesai,dibatalkan',
            'keterangan'               => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.detail_id'        => 'nullable|integer',
            'items.*.inventory_id'     => 'nullable|integer',
            'items.*.kondisi'          => 'nullable|in:baru,bekas',
            'items.*.nama_alat'        => 'required|string|max:255',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.satuan'           => 'required|string|max:50',
            'items.*.harga_satuan'     => 'required|integer|min:0',
            'items.*.diskon'           => 'nullable|integer|min:0|max:100',
        ], [
            'nomor_ktp.size'  => 'Nomor KTP harus tepat 16 digit.',
            'nomor_ktp.regex' => 'Nomor KTP hanya boleh berisi angka.',
        ]);

        // Hitung ulang durasi dari tanggal yang diinput user (bisa berubah saat edit)
        $validated['durasi_hari'] = $this->hitungDurasi(
            $validated['tgl_mulai'],
            $validated['tgl_selesai']
        );

        if ($request->hasFile('foto_ktp_sim')) {
            if ($penyewaan->foto_ktp_sim &&
                \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
                \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
            }
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        }

        $validated['biaya_ongkir']  = $validated['biaya_ongkir']  ?? 0;
        $validated['diskon_global'] = $validated['diskon_global'] ?? 0;

        $penyewaan->update(collect($validated)->except('items')->toArray());

        $this->syncDetails($penyewaan, $validated['items']);

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'update',
            subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
            oldValue: $oldData,
            newValue: [
                'penyewa'     => $penyewaan->fresh()->nama_penyewa,
                'status'      => $penyewaan->fresh()->status,
                'tgl_selesai' => $penyewaan->fresh()->tgl_selesai?->format('d M Y'),
                'total'       => 'Rp ' . number_format($penyewaan->fresh()->total_tagihan, 0, ',', '.'),
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

        foreach ($penyewaan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru', true);
                }
            }
        }

        ActivityLog::record(
            module:   'Penyewaan',
            action:   'delete',
            subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
            oldValue: [
                'penyewa'     => $penyewaan->nama_penyewa,
                'nomor_hp'    => $penyewaan->nomor_telepon,
                'tgl_mulai'   => $penyewaan->tgl_mulai?->format('d M Y'),
                'tgl_selesai' => $penyewaan->tgl_selesai?->format('d M Y'),
                'total'       => 'Rp ' . number_format($penyewaan->total_tagihan, 0, ',', '.'),
            ],
            pageUrl: 'penyewaan'
        );

        if ($penyewaan->foto_ktp_sim &&
            \Storage::disk('public')->exists($penyewaan->foto_ktp_sim)) {
            \Storage::disk('public')->delete($penyewaan->foto_ktp_sim);
        }

        $penyewaan->delete();

        return redirect()->route('penyewaan.index')
            ->with('success', 'Data penyewaan berhasil dihapus.');
    }

    // =========================================================
    //  CETAK INVOICE & PERJANJIAN
    // =========================================================

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