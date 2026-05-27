<?php
// app/Http/Controllers/PenjualanController.php

namespace App\Http\Controllers;

use App\Exports\PenjualanExport;                // ← TAMBAH
use App\Models\ActivityLog;
use App\Models\DetailPenjualan;
use App\Models\Inventory;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;            // ← TAMBAH

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

            $qtyBaru      = max(1, (int) ($item['qty']          ?? 1));
            $harga        = max(0, (int) ($item['harga_satuan'] ?? 0));
            $diskon       = max(0, min(100, (int) ($item['diskon'] ?? 0)));
            $inventoryId  = !empty($item['inventory_id']) ? (int) $item['inventory_id'] : null;
            $kondisi      = in_array($item['kondisi'] ?? '', ['baru','bekas'])
                            ? $item['kondisi']
                            : 'baru';
            $subtotal     = (int) round($qtyBaru * $harga * (1 - $diskon / 100));

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
                /** @var Inventory $inv */
                $inv = Inventory::find($inventoryId);
                if ($inv) {
                    $qtyLama     = $detailLama ? (int) $detailLama->qty : 0;
                    $kondisiLama = $detailLama ? $detailLama->kondisi : $kondisi;
                    $selisih     = $qtyBaru - $qtyLama;

                    if ($detailLama && $kondisiLama !== $kondisi) {
                        if ($qtyLama > 0) {
                            $inv->tambahStok($qtyLama, $kondisiLama);
                        }
                        $inv->kurangiStok($qtyBaru, $kondisi);
                    } else {
                        if ($selisih > 0) {
                            $inv->kurangiStok($selisih, $kondisi);
                        } elseif ($selisih < 0) {
                            $inv->tambahStok(abs($selisih), $kondisi);
                        }
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

        $toDelete = array_diff($existingIds, $submittedIds);
        if (!empty($toDelete)) {
            $detailsToDelete = DetailPenjualan::whereIn('id', $toDelete)->get();
            foreach ($detailsToDelete as $detail) {
                if ($detail->inventory_id) {
                    $inv = Inventory::find($detail->inventory_id);
                    if ($inv) {
                        $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru');
                    }
                }
            }
            DetailPenjualan::whereIn('id', $toDelete)->delete();
        }

        $penjualan->update(['total_harga' => $totalHarga]);
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

        $penjualans = Penjualan::with('details')
            ->when($search, function ($q) use ($search) {
                $q->where('nama_pelanggan',     'like', "%{$search}%")
                  ->orWhere('nomor_telepon',    'like', "%{$search}%")
                  ->orWhere('alamat_pelanggan', 'like', "%{$search}%")
                  ->orWhere('jenis_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan',       'like', "%{$search}%")
                  ->orWhere('tanggal_penjualan','like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penjualan.index', compact('penjualans', 'search', 'perPage'));
    }

    // =========================================================
    //  EXPORT XLSX  ← TAMBAH METHOD INI
    // =========================================================

    public function export(Request $request)
    {
        $search   = $request->input('search', '');
        $filename = 'penjualan_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PenjualanExport($search), $filename);
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
            'nama_pelanggan'           => 'required|string|max:255',
            'nomor_telepon'            => 'nullable|string|max:20',
            'alamat_pelanggan'         => 'required|string',
            'tanggal_penjualan'        => 'required|date',
            'jenis_pembayaran'         => 'required|in:tunai,transfer,qris,kredit',
            'diskon_global'            => 'nullable|integer|min:0',
            'foto_bukti'               => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'keterangan'               => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.inventory_id'     => 'nullable|integer|exists:inventories,id',
            'items.*.nama_barang'      => 'required|string|max:255',
            'items.*.kondisi'          => 'required|in:baru,bekas',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.satuan'           => 'required|string|max:50',
            'items.*.harga_satuan'     => 'required|integer|min:0',
            'items.*.diskon'           => 'nullable|integer|min:0|max:100',
        ]);

        if ($request->hasFile('foto_bukti')) {
            $validated['foto_bukti'] = $request->file('foto_bukti')
                ->store('penjualan/bukti', 'public');
        }

        $validated['diskon_global'] = $validated['diskon_global'] ?? 0;
        $validated['total_harga']   = 0;

        $penjualan = Penjualan::create(collect($validated)->except('items')->toArray());

        $this->syncDetails($penjualan, $validated['items']);

        ActivityLog::record(
            module:   'Penjualan',
            action:   'create',
            subject:  'No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            newValue: [
                'pelanggan' => $penjualan->nama_pelanggan,
                'barang'    => collect($validated['items'])->pluck('nama_barang')->implode(', '),
                'tanggal'   => $penjualan->tanggal_penjualan->format('d M Y'),
                'total'     => 'Rp ' . number_format($penjualan->total_tagihan, 0, ',', '.'),
            ],
            pageUrl: 'penjualan'
        );

        return redirect()->route('penjualan.index')
            ->with('success', 'Data penjualan berhasil ditambahkan.');
    }

    // =========================================================
    //  SHOW
    // =========================================================

    public function show(string $id)
    {
        $penjualan = Penjualan::with('details.inventory')->findOrFail($id);
        return view('admin.penjualan.show', compact('penjualan'));
    }

    // =========================================================
    //  EDIT & UPDATE
    // =========================================================

    public function edit(string $id)
    {
        $penjualan = Penjualan::with('details')->findOrFail($id);
        return view('admin.penjualan.edit', compact('penjualan'));
    }

    public function update(Request $request, string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        $oldData = [
            'pelanggan' => $penjualan->nama_pelanggan,
            'tanggal'   => $penjualan->tanggal_penjualan?->format('d M Y'),
            'total'     => 'Rp ' . number_format($penjualan->total_tagihan, 0, ',', '.'),
        ];

        $validated = $request->validate([
            'nama_pelanggan'           => 'required|string|max:255',
            'nomor_telepon'            => 'nullable|string|max:20',
            'alamat_pelanggan'         => 'required|string',
            'tanggal_penjualan'        => 'required|date',
            'jenis_pembayaran'         => 'required|in:tunai,transfer,qris,kredit',
            'diskon_global'            => 'nullable|integer|min:0',
            'foto_bukti'               => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'keterangan'               => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.detail_id'        => 'nullable|integer',
            'items.*.inventory_id'     => 'nullable|integer|exists:inventories,id',
            'items.*.nama_barang'      => 'required|string|max:255',
            'items.*.kondisi'          => 'required|in:baru,bekas',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.satuan'           => 'required|string|max:50',
            'items.*.harga_satuan'     => 'required|integer|min:0',
            'items.*.diskon'           => 'nullable|integer|min:0|max:100',
        ]);

        if ($request->hasFile('foto_bukti')) {
            if ($penjualan->foto_bukti &&
                \Storage::disk('public')->exists($penjualan->foto_bukti)) {
                \Storage::disk('public')->delete($penjualan->foto_bukti);
            }
            $validated['foto_bukti'] = $request->file('foto_bukti')
                ->store('penjualan/bukti', 'public');
        }

        $validated['diskon_global'] = $validated['diskon_global'] ?? 0;

        $penjualan->update(collect($validated)->except('items')->toArray());

        $this->syncDetails($penjualan, $validated['items']);

        ActivityLog::record(
            module:   'Penjualan',
            action:   'update',
            subject:  'No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            oldValue: $oldData,
            newValue: [
                'pelanggan' => $penjualan->fresh()->nama_pelanggan,
                'tanggal'   => $penjualan->fresh()->tanggal_penjualan?->format('d M Y'),
                'total'     => 'Rp ' . number_format($penjualan->fresh()->total_tagihan, 0, ',', '.'),
            ],
            pageUrl: 'penjualan/' . $penjualan->id . '/edit'
        );

        return redirect()->route('penjualan.index')
            ->with('success', 'Data penjualan berhasil diperbarui.');
    }

    // =========================================================
    //  DESTROY
    // =========================================================

    public function destroy(string $id)
    {
        $penjualan = Penjualan::with('details')->findOrFail($id);

        foreach ($penjualan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru');
                }
            }
        }

        ActivityLog::record(
            module:   'Penjualan',
            action:   'delete',
            subject:  'No. Jual #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            oldValue: [
                'pelanggan' => $penjualan->nama_pelanggan,
                'tanggal'   => $penjualan->tanggal_penjualan?->format('d M Y'),
                'total'     => 'Rp ' . number_format($penjualan->total_tagihan, 0, ',', '.'),
            ],
            pageUrl: 'penjualan'
        );

        if ($penjualan->foto_bukti &&
            \Storage::disk('public')->exists($penjualan->foto_bukti)) {
            \Storage::disk('public')->delete($penjualan->foto_bukti);
        }

        $penjualan->delete();

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