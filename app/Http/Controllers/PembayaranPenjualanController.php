<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PembayaranPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranPenjualanController extends Controller
{
    // =========================================================
    //  STORE — Tambah pembayaran baru (DP atau Pelunasan)
    // =========================================================

    public function store(Request $request, string $penjualanId)
    {
        $penjualan = Penjualan::findOrFail($penjualanId);

        // Tidak bisa tambah bayar kalau sudah lunas atau dibatalkan
        if ($penjualan->isBatal()) {
            return back()->with('error', 'Transaksi ini sudah dibatalkan.');
        }

        if ($penjualan->isLunas()) {
            return back()->with('error', 'Transaksi ini sudah lunas.');
        }

        $validated = $request->validate([
            'tipe'        => 'required|in:dp,pelunasan,cicilan',
            'metode'      => 'required|in:cash,transfer,qris',
            'jumlah_bayar'=> 'required|integer|min:1',
            'foto_bukti'  => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'keterangan'  => 'nullable|string|max:500',
            'tanggal_bayar' => 'required|date',
        ]);

        // Validasi jumlah tidak melebihi sisa tagihan
        $sisaTagihan = $penjualan->sisa_tagihan;
        if ($validated['jumlah_bayar'] > $sisaTagihan) {
            return back()
                ->withInput()
                ->with('error', 'Jumlah bayar (Rp ' . number_format($validated['jumlah_bayar'], 0, ',', '.') . ') melebihi sisa tagihan (Rp ' . number_format($sisaTagihan, 0, ',', '.') . ').');
        }

        if ($request->hasFile('foto_bukti')) {
            $validated['foto_bukti'] = $request->file('foto_bukti')
                ->store('penjualan/pembayaran', 'public');
        }

        $validated['penjualan_id'] = $penjualan->id;
        $validated['created_by']   = auth()->id();

        PembayaranPenjualan::create($validated);

        // Observer akan otomatis sync total_terbayar & status_pembayaran

        ActivityLog::record(
            module:   'Penjualan',
            action:   'update',
            subject:  'Pembayaran #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            newValue: [
                'tipe'         => $validated['tipe'],
                'jumlah_bayar' => 'Rp ' . number_format($validated['jumlah_bayar'], 0, ',', '.'),
                'metode'       => $validated['metode'],
            ],
            pageUrl: 'penjualan/' . $penjualan->id
        );

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    // =========================================================
    //  DESTROY — Hapus record pembayaran
    // =========================================================

    public function destroy(string $id)
    {
        $pembayaran = PembayaranPenjualan::with('penjualan')->findOrFail($id);
        $penjualan  = $pembayaran->penjualan;

        // Jangan hapus kalau transaksi sudah selesai/batal
        if ($penjualan && $penjualan->status_transaksi === 'selesai') {
            return back()->with('error', 'Pembayaran pada transaksi selesai tidak dapat dihapus.');
        }

        if ($pembayaran->foto_bukti && Storage::disk('public')->exists($pembayaran->foto_bukti)) {
            Storage::disk('public')->delete($pembayaran->foto_bukti);
        }

        $pembayaran->delete();
        // Observer akan otomatis recalculate

        return back()->with('success', 'Pembayaran berhasil dihapus.');
    }

    // =========================================================
    //  BATAL TRANSAKSI
    // =========================================================

    public function batalTransaksi(Request $request, string $penjualanId)
    {
        $penjualan = Penjualan::with('details')->findOrFail($penjualanId);

        if ($penjualan->isBatal()) {
            return back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        $request->validate([
            'catatan_pembatalan' => 'required|string|max:500',
        ]);

        // Rollback stok manual (karena tidak pakai observer penjualan)
        foreach ($penjualan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = \App\Models\Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->tambahStok($detail->qty, $detail->kondisi ?? 'baru');

                    \App\Models\InventoryLog::create([
                        'inventory_id'   => $inv->id,
                        'reference_type' => 'sale_cancelled',
                        'reference_id'   => $penjualan->id,
                        'qty_change'     => $detail->qty,
                        'kondisi'        => $detail->kondisi ?? 'baru',
                        'stok_sebelum'   => $inv->stok_tersedia - $detail->qty,
                        'stok_sesudah'   => $inv->stok_tersedia,
                        'keterangan'     => 'Pembatalan Penjualan #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
                        'created_by'     => auth()->id(),
                    ]);
                }
            }
        }

        $penjualan->withoutObservers(function () use ($penjualan, $request) {
            $penjualan->update([
                'status_transaksi'   => 'batal',
                'catatan_pembatalan' => $request->catatan_pembatalan,
            ]);
        });

        ActivityLog::record(
            module:   'Penjualan',
            action:   'update',
            subject:  'Batal Transaksi #' . $penjualan->id . ' — ' . $penjualan->nama_pelanggan,
            newValue: ['alasan' => $request->catatan_pembatalan],
            pageUrl:  'penjualan/' . $penjualan->id
        );

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', 'Transaksi berhasil dibatalkan dan stok sudah dikembalikan.');
    }
}