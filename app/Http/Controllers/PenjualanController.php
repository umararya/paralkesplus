<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('admin.penjualan.index');
    }

    public function create()
    {
        return view('admin.penjualan.create');
    }

    public function store(Request $request)
    {
        // TODO: Implementasi simpan data penjualan
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return view('admin.penjualan.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('admin.penjualan.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        // TODO: Implementasi update data penjualan
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // TODO: Implementasi hapus data penjualan
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil dihapus.');
    }
}