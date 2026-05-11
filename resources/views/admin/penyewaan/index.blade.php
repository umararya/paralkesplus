@extends('admin.layouts.app')

@section('title', 'Penyewaan')
@section('breadcrumb', 'Penyewaan')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">Manajemen Penyewaan</h1>
        <p style="font-size:13px; color:var(--text-muted);">Kelola data penyewaan alat kesehatan</p>
    </div>
    <a href="{{ route('penyewaan.create') }}"
       style="display:inline-flex; align-items:center; gap:8px; padding:10px 18px;
              background:var(--brand-500); color:white; border-radius:8px;
              font-size:13px; font-weight:600; text-decoration:none;
              transition:background 0.2s;"
       onmouseover="this.style.background='var(--brand-600)'"
       onmouseout="this.style.background='var(--brand-500)'">
        <i class="ri-add-line"></i> Tambah Penyewaan
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
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary); white-space:nowrap;">#</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Nama Penyewa</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Alat Kesehatan</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Tanggal Sewa</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Tanggal Kembali</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Status</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" style="padding:48px 16px; text-align:center; color:var(--text-muted);">
                        <i class="ri-store-2-line" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.4;"></i>
                        Belum ada data penyewaan
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection