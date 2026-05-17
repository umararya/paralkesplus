<?php
// app/Http/Controllers/PenjualanController.php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        $penjualans = Penjualan::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('alamat_pelanggan', 'like', "%{$search}%")
                  ->orWhere('jenis_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('tanggal_penjualan', 'like', "%{$search}%")
                  ->orWhereRaw('CAST(qty AS CHAR) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('CAST(harga AS CHAR) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('CAST(total AS CHAR) LIKE ?', ["%{$search}%"]);
            })
            ->orderBy('tanggal_penjualan', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penjualan.index', compact('penjualans', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.penjualan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_penjualan' => 'required|date',
            'nama_barang'       => 'required|string|max:255',
            'qty'               => 'required|integer|min:1',
            'alamat_pelanggan'  => 'required|string|max:500',
            'jenis_pembayaran'  => 'required|in:tunai,transfer,qris,kredit',
            'harga'             => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string|max:1000',
            'foto_bukti'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto_bukti')) {
            $validated['foto_bukti'] = $request->file('foto_bukti')
                ->store('penjualan/bukti', 'public');
        }

        $penjualan = Penjualan::create($validated);

        // ── Activity Log ──
        ActivityLog::record(
            module:   'Penjualan',
            action:   'create',
            subject:  $penjualan->nama_barang,
            newValue: [
                'barang'    => $penjualan->nama_barang,
                'qty'       => $penjualan->qty,
                'total'     => 'Rp ' . number_format($penjualan->total, 0, ',', '.'),
                'pembayaran'=> $penjualan->jenis_pembayaran,
                'tanggal'   => $penjualan->tanggal_penjualan,
            ],
            pageUrl: 'penjualan'
        );

        return redirect()->route('penjualan.index')
                         ->with('success', 'Data penjualan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('admin.penjualan.show', compact('penjualan'));
    }

    public function edit(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('admin.penjualan.edit', compact('penjualan'));
    }

    public function update(Request $request, string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        $validated = $request->validate([
            'tanggal_penjualan' => 'required|date',
            'nama_barang'       => 'required|string|max:255',
            'qty'               => 'required|integer|min:1',
            'alamat_pelanggan'  => 'required|string|max:500',
            'jenis_pembayaran'  => 'required|in:tunai,transfer,qris,kredit',
            'harga'             => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string|max:1000',
            'foto_bukti'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hapus_foto'        => 'nullable|boolean',
        ]);

        // Simpan data lama sebelum update
        $oldData = [
            'barang'     => $penjualan->nama_barang,
            'qty'        => $penjualan->qty,
            'total'      => 'Rp ' . number_format($penjualan->total, 0, ',', '.'),
            'pembayaran' => $penjualan->jenis_pembayaran,
        ];

        if ($request->boolean('hapus_foto') && $penjualan->foto_bukti) {
            Storage::disk('public')->delete($penjualan->foto_bukti);
            $validated['foto_bukti'] = null;
        }

        if ($request->hasFile('foto_bukti')) {
            if ($penjualan->foto_bukti) {
                Storage::disk('public')->delete($penjualan->foto_bukti);
            }
            $validated['foto_bukti'] = $request->file('foto_bukti')
                ->store('penjualan/bukti', 'public');
        }

        $penjualan->update($validated);

        // ── Activity Log ──
        ActivityLog::record(
            module:   'Penjualan',
            action:   'update',
            subject:  $penjualan->nama_barang,
            oldValue: $oldData,
            newValue: [
                'barang'     => $penjualan->nama_barang,
                'qty'        => $penjualan->qty,
                'total'      => 'Rp ' . number_format($penjualan->total, 0, ',', '.'),
                'pembayaran' => $penjualan->jenis_pembayaran,
            ],
            pageUrl: 'penjualan/' . $penjualan->id . '/edit'
        );

        return redirect()->route('penjualan.index')
                         ->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        // ── Activity Log (SEBELUM delete) ──
        ActivityLog::record(
            module:   'Penjualan',
            action:   'delete',
            subject:  $penjualan->nama_barang,
            oldValue: [
                'barang'    => $penjualan->nama_barang,
                'qty'       => $penjualan->qty,
                'total'     => 'Rp ' . number_format($penjualan->total, 0, ',', '.'),
                'tanggal'   => $penjualan->tanggal_penjualan,
                'pembayaran'=> $penjualan->jenis_pembayaran,
            ],
            pageUrl: 'penjualan'
        );

        if ($penjualan->foto_bukti) {
            Storage::disk('public')->delete($penjualan->foto_bukti);
        }

        $penjualan->delete();

        return redirect()->route('penjualan.index')
                         ->with('success', 'Data penjualan berhasil dihapus.');
    }
}