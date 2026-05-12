<?php
// app/Http/Controllers/PenyewaanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// Uncomment baris ini setelah Model dibuat:
// use App\Models\Penyewaan;

class PenyewaanController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
                   ? (int) $request->input('per_page')
                   : 10;

        // ── Gunakan ini setelah Model Penyewaan dibuat ──
        // $penyewaans = Penyewaan::query()
        //     ->when($search, function ($q) use ($search) {
        //         $q->where('nama_penyewa', 'like', "%{$search}%")
        //           ->orWhere('nama_alat', 'like', "%{$search}%")
        //           ->orWhere('status', 'like', "%{$search}%");
        //     })
        //     ->orderBy('tanggal_sewa', 'desc')
        //     ->paginate($perPage)
        //     ->withQueryString();

        // ── Placeholder sementara (hapus setelah Model dibuat) ──
        $penyewaans = new LengthAwarePaginator(
            new Collection([]),
            0, $perPage, 1,
            ['path' => route('penyewaan.index')]
        );

        return view('admin.penyewaan.index', compact('penyewaans', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.penyewaan.create');
    }

    public function store(Request $request)
    {
        // TODO: Implementasi simpan data penyewaan
        return redirect()->route('penyewaan.index')->with('success', 'Data penyewaan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return view('admin.penyewaan.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('admin.penyewaan.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        // TODO: Implementasi update data penyewaan
        return redirect()->route('penyewaan.index')->with('success', 'Data penyewaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // TODO: Implementasi hapus data penyewaan
        return redirect()->route('penyewaan.index')->with('success', 'Data penyewaan berhasil dihapus.');
    }
}