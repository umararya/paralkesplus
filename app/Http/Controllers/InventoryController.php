<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return view('admin.inventory.index');
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        // TODO: Implementasi simpan data inventory
        return redirect()->route('inventory.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return view('admin.inventory.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('admin.inventory.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        // TODO: Implementasi update data inventory
        return redirect()->route('inventory.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // TODO: Implementasi hapus data inventory
        return redirect()->route('inventory.index')->with('success', 'Barang berhasil dihapus.');
    }
}