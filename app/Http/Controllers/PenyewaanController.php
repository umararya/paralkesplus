<?php
// app/Http/Controllers/PenyewaanController.php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DetailPenyewaan;
use App\Models\Inventory;
use App\Models\Penyewaan;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            $item->update(['status' => 'selesai']);
            // Kembalikan stok saat otomatis selesai
            $this->kembalikanStok($item);
            return;
        }

        if ($sisaHari <= 3 && $item->status === 'berjalan') {
            $item->update(['status' => 'segera_konfirmasi']);
            return;
        }

        if ($sisaHari > 3 && $item->status === 'segera_konfirmasi') {
            $item->update(['status' => 'berjalan']);
        }
    }

    // =========================================================
    //  PRIVATE HELPER — kembalikan stok semua detail penyewaan
    // =========================================================

    private function kembalikanStok(Penyewaan $penyewaan): void
    {
        // Eager load details jika belum dimuat
        if (! $penyewaan->relationLoaded('details')) {
            $penyewaan->load('details');
        }

        foreach ($penyewaan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    // dariSewa = true: akan mengurangi stok_disewa sekaligus menambah stok_tersedia
                    $inv->tambahStok($detail->qty, 'baru', true);
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

            $qtyBaru      = max(1, (int) ($item['qty']          ?? 1));
            $harga        = max(0, (int) ($item['harga_satuan'] ?? 0));
            $diskon       = max(0, min(100, (int) ($item['diskon'] ?? 0)));
            $inventoryId  = !empty($item['inventory_id']) ? (int) $item['inventory_id'] : null;
            $subtotal     = (int) round($qtyBaru * $harga * (1 - $diskon / 100));

            $data = [
                'penyewaan_id' => $penyewaan->id,
                'inventory_id' => $inventoryId,
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

            // Update stok dengan flag untukSewa = true
            if ($inventoryId) {
                /** @var Inventory $inv */
                $inv = Inventory::find($inventoryId);
                if ($inv) {
                    $qtyLama = $detailLama ? (int) $detailLama->qty : 0;
                    $selisih = $qtyBaru - $qtyLama;

                    if ($selisih > 0) {
                        // Tambah sewa → kurangi stok_tersedia, tambah stok_disewa
                        $inv->kurangiStok($selisih, 'baru', true);
                    } elseif ($selisih < 0) {
                        // Kurangi sewa → kembalikan stok_tersedia, kurangi stok_disewa
                        $inv->tambahStok(abs($selisih), 'baru', true);
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

        // Hapus detail yang tidak ada + kembalikan stok
        $toDelete = array_diff($existingIds, $submittedIds);
        if (!empty($toDelete)) {
            $detailsToDelete = DetailPenyewaan::whereIn('id', $toDelete)->get();
            foreach ($detailsToDelete as $detail) {
                if ($detail->inventory_id) {
                    $inv = Inventory::find($detail->inventory_id);
                    if ($inv) {
                        // dariSewa = true: kurangi stok_disewa, tambah stok_tersedia
                        $inv->tambahStok($detail->qty, 'baru', true);
                    }
                }
            }
            DetailPenyewaan::whereIn('id', $toDelete)->delete();
        }

        $penyewaan->update(['total_harga_sewa' => $totalSewa]);
    }

    // =========================================================
    //  INDEX
    // =========================================================

    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        $aktif = Penyewaan::whereIn('status', ['berjalan', 'segera_konfirmasi'])->get();
        foreach ($aktif as $item) {
            $this->syncStatus($item);
        }

        $penyewaans = Penyewaan::with('details')  // <-- WAJIB: eager load untuk accessor nama_alat
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
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penyewaan.index', compact('penyewaans', 'search', 'perPage'));
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
                    'id'           => $item->id,
                    'nama'         => $item->nama_penyewa,
                    'nomor_hp'     => $item->nomor_telepon,
                    'barang'       => $barang,
                    'alamat'       => $item->alamat_penyewa,
                    'sisa_hari'    => $item->sisa_hari,
                    'tgl_selesai'  => $item->tgl_selesai?->format('d M Y') ?? '-',
                    'status'       => $item->status,
                    'status_label' => $item->status_label,
                    'status_class' => $item->status_class,
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
                    $sisaHari <= 0 => 'Lewat deadline!',
                    $sisaHari === 1 => 'Besok deadline!',
                    $sisaHari === 2 => '2 hari lagi',
                    default        => $sisaHari . ' hari lagi',
                };

                $barang = $item->details->count()
                    ? $item->details->pluck('nama_alat')->implode(', ')
                    : ($item->produk_alkes ?? '-');

                return [
                    'id'          => $item->id,
                    'nama'        => $item->nama_penyewa,
                    'barang'      => $barang,
                    'sisa_hari'   => $sisaHari,
                    'sisa_label'  => $sisaLabel,
                    'tgl_selesai' => $item->tgl_selesai?->format('d M Y') ?? '-',
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

            $penyewaan->update([
                'status'      => 'selesai',
                'tgl_selesai' => Carbon::today()->format('Y-m-d'),
            ]);

            // PERBAIKAN: Kembalikan stok semua barang yang disewa
            $this->kembalikanStok($penyewaan);

            ActivityLog::record(
                module:   'Penyewaan',
                action:   'update',
                subject:  'No. Sewa #' . $penyewaan->id . ' — ' . $penyewaan->nama_penyewa,
                oldValue: ['status' => $oldStatus],
                newValue: ['status' => 'selesai', 'tgl_selesai' => Carbon::today()->format('d M Y')],
                pageUrl:  'penyewaan/' . $penyewaan->id . '/selesaikan'
            );

            return response()->json([
                'success' => true,
                'action'  => 'selesai_sekarang',
                'message' => 'Penyewaan berhasil diselesaikan dan stok barang telah dikembalikan.',
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
        $tglMulai   = Carbon::parse($penyewaan->tgl_mulai)->startOfDay();
        $durasiHari = (int) $tglMulai->diffInDays($tglBaru);
        $sisaBaru   = (int) Carbon::today()->diffInDays($tglBaru, false);
        $newStatus  = $sisaBaru > 3 ? 'berjalan' : 'segera_konfirmasi';

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
            'durasi_hari'              => 'required|integer|min:0',
            'pengiriman'               => 'required|in:mandiri,Gosend / GrabExpress,Rental Mobil Paralkes',
            'biaya_ongkir'             => 'nullable|integer|min:0',
            'diskon_global'            => 'nullable|integer|min:0',
            'metode_pembayaran'        => 'required|in:tunai,transfer,qris',
            'bukti_pembayaran'         => 'nullable|string|max:500',
            'foto_ktp_sim'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'keterangan'               => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.inventory_id'     => 'nullable|integer',
            'items.*.nama_alat'        => 'required|string|max:255',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.satuan'           => 'required|string|max:50',
            'items.*.harga_satuan'     => 'required|integer|min:0',
            'items.*.diskon'           => 'nullable|integer|min:0|max:100',
        ], [
            'nomor_ktp.size'  => 'Nomor KTP harus tepat 16 digit.',
            'nomor_ktp.regex' => 'Nomor KTP hanya boleh berisi angka.',
        ]);

        if ($request->hasFile('foto_ktp_sim')) {
            $validated['foto_ktp_sim'] = $request->file('foto_ktp_sim')
                ->store('penyewaan/ktp', 'public');
        }

        $validated['biaya_ongkir']     = $validated['biaya_ongkir']  ?? 0;
        $validated['diskon_global']    = $validated['diskon_global'] ?? 0;
        $validated['total_harga_sewa'] = 0;
        $validated['status']           = 'berjalan';

        $penyewaan = Penyewaan::create(collect($validated)->except('items')->toArray());

        // sync details + update stok (untukSewa = true sudah ditangani di syncDetails)
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
            'durasi_hari'              => 'required|integer|min:0',
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
            'items.*.nama_alat'        => 'required|string|max:255',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.satuan'           => 'required|string|max:50',
            'items.*.harga_satuan'     => 'required|integer|min:0',
            'items.*.diskon'           => 'nullable|integer|min:0|max:100',
        ], [
            'nomor_ktp.size'  => 'Nomor KTP harus tepat 16 digit.',
            'nomor_ktp.regex' => 'Nomor KTP hanya boleh berisi angka.',
        ]);

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

        // sync details + update stok (diff, dengan flag sewa)
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

        // Kembalikan semua stok ketika penyewaan dihapus (dariSewa = true)
        foreach ($penyewaan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->tambahStok($detail->qty, 'baru', true);
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

        $penyewaan->delete(); // details ikut cascade delete

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