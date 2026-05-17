<?php
// app/Http/Controllers/PembelianController.php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pembelian;
use Illuminate\Http\Request;
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
        return view('admin.pembelian.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $validated['status'] = 'normal';

        if ($request->hasFile('bukti_transaksi')) {
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')
                ->store('pembelian/bukti', 'public');
        }

        $pembelian = Pembelian::create($validated);

        // ── Activity Log ──
        ActivityLog::record(
            module:   'Pembelian',
            action:   'create',
            subject:  $pembelian->nama_barang,
            newValue: [
                'barang'   => $pembelian->nama_barang,
                'jumlah'   => $pembelian->jumlah,
                'total'    => 'Rp ' . number_format($pembelian->total, 0, ',', '.'),
                'tanggal'  => $pembelian->tanggal_pembelian,
            ],
            pageUrl: 'pembelian'
        );

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        return view('admin.pembelian.show', compact('pembelian'));
    }

    public function edit(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        return view('admin.pembelian.edit', compact('pembelian'));
    }

    public function update(Request $request, string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'nama_barang'       => 'required|string|max:150',
            'jumlah'            => 'required|integer|min:1',
            'harga_satuan'      => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string',
            'bukti_transaksi'   => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // Simpan data lama sebelum update
        $oldData = [
            'barang'  => $pembelian->nama_barang,
            'jumlah'  => $pembelian->jumlah,
            'total'   => 'Rp ' . number_format($pembelian->total, 0, ',', '.'),
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

        $pembelian->update($validated);

        // ── Activity Log ──
        ActivityLog::record(
            module:   'Pembelian',
            action:   'update',
            subject:  $pembelian->nama_barang,
            oldValue: $oldData,
            newValue: [
                'barang'  => $pembelian->nama_barang,
                'jumlah'  => $pembelian->jumlah,
                'total'   => 'Rp ' . number_format($pembelian->total, 0, ',', '.'),
            ],
            pageUrl: 'pembelian/' . $pembelian->id . '/edit'
        );

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        // ── Activity Log (SEBELUM delete) ──
        ActivityLog::record(
            module:   'Pembelian',
            action:   'delete',
            subject:  $pembelian->nama_barang,
            oldValue: [
                'barang'   => $pembelian->nama_barang,
                'jumlah'   => $pembelian->jumlah,
                'total'    => 'Rp ' . number_format($pembelian->total, 0, ',', '.'),
                'tanggal'  => $pembelian->tanggal_pembelian,
                'status'   => $pembelian->status,
            ],
            pageUrl: 'pembelian'
        );

        if ($pembelian->bukti_transaksi) {
            Storage::disk('public')->delete($pembelian->bukti_transaksi);
        }

        $pembelian->delete();

        return redirect()->route('pembelian.index')
            ->with('success', 'Data pembelian berhasil dihapus.');
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

        $pembelian = Pembelian::create($validated);

        // ── Activity Log ──
        ActivityLog::record(
            module:   'Pembelian',
            action:   'create',
            subject:  '[Buy Back] ' . $pembelian->nama_barang . ' dari ' . $pembelian->nama_pelanggan,
            newValue: [
                'barang'    => $pembelian->nama_barang,
                'pelanggan' => $pembelian->nama_pelanggan,
                'kondisi'   => $pembelian->kondisi_barang,
                'jumlah'    => $pembelian->jumlah,
                'total'     => 'Rp ' . number_format($pembelian->total, 0, ',', '.'),
            ],
            pageUrl: 'pembelian/buy-back'
        );

        return redirect()->route('penjualan.index')
            ->with('success', 'Buy back berhasil dicatat dan masuk ke data pembelian.');
    }
}