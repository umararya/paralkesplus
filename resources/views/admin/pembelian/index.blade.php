@extends('admin.layouts.app')

@section('title', 'Pembelian Barang')
@section('breadcrumb', 'Pembelian Barang')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">Manajemen Pembelian Barang</h1>
        <p style="font-size:13px; color:var(--text-muted);">Kelola data pembelian barang dan alat kesehatan</p>
    </div>
    <a href="{{ route('pembelian.create') }}"
       style="display:inline-flex; align-items:center; gap:8px; padding:10px 18px;
              background:var(--brand-500); color:white; border-radius:8px;
              font-size:13px; font-weight:600; text-decoration:none; transition:background 0.2s;"
       onmouseover="this.style.background='var(--brand-600)'"
       onmouseout="this.style.background='var(--brand-500)'">
        <i class="ri-add-line"></i> Tambah Pembelian
    </a>
</div>

@if(session('success'))
<div style="background:#DCFCE7; border:1px solid #86EFAC; color:#166534;
            padding:12px 16px; border-radius:8px; margin-bottom:20px;
            font-size:13px; display:flex; align-items:center; gap:8px;">
    <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
</div>
@endif

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">#</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">Nama Barang</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">Supplier</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">Jumlah</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">Harga Satuan</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">Total</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">Tanggal</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="8" style="padding:48px 16px; text-align:center; color:var(--text-muted);">
                        <i class="ri-shopping-cart-2-line" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.4;"></i>
                        Belum ada data pembelian barang
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection