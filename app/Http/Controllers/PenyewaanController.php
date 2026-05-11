<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyewaanController extends Controller
{
    public function index()
    {
        return view('admin.penyewaan.index');
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