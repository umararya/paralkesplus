<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'inventory_id',
        'reference_type',
        'reference_id',
        'qty_change',
        'kondisi',
        'keterangan',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Badge label untuk reference_type
     */
    public function getReferenceTypeLabelAttribute(): string
    {
        return match($this->reference_type) {
            'purchase'       => 'Pembelian',
            'buyback'        => 'Buy Back',
            'sale'           => 'Penjualan',
            'rental_start'   => 'Sewa Keluar',
            'rental_return'  => 'Sewa Kembali',
            default          => ucfirst($this->reference_type),
        };
    }

    /**
     * Warna badge untuk Tailwind
     */
    public function getReferenceTypeColorAttribute(): string
    {
        return match($this->reference_type) {
            'purchase'       => 'bg-blue-100 text-blue-800',
            'buyback'        => 'bg-purple-100 text-purple-800',
            'sale'           => 'bg-red-100 text-red-800',
            'rental_start'   => 'bg-orange-100 text-orange-800',
            'rental_return'  => 'bg-green-100 text-green-800',
            default          => 'bg-gray-100 text-gray-800',
        };
    }
}