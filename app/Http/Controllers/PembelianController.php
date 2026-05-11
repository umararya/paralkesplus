<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index()
    {
        return view('admin.pembelian.index');
    }

    public function create()
    {
        return view('admin.pembelian.create');
    }

    public function store(Request $request)
    {
        // TODO: Implementasi simpan data pembelian
        return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return view('admin.pembelian.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('admin.pembelian.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        // TODO: Implementasi update data pembelian
        return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // TODO: Implementasi hapus data pembelian
        return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil dihapus.');
    }
}