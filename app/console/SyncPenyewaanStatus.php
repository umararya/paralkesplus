<?php
// app/Console/Commands/SyncPenyewaanStatus.php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\Penyewaan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncPenyewaanStatus extends Command
{
    protected $signature   = 'penyewaan:sync-status';
    protected $description = 'Sync status penyewaan secara otomatis berdasarkan tanggal selesai';

    public function handle(): int
    {
        $this->info('[' . now()->format('d M Y H:i') . '] Mulai sync status penyewaan...');

        $aktif = Penyewaan::with('details')
            ->whereIn('status', ['berjalan', 'segera_konfirmasi'])
            ->get();

        $countSelesai   = 0;
        $countKonfirmasi = 0;
        $countBerjalan  = 0;

        foreach ($aktif as $item) {
            $sisaHari = $item->sisa_hari;

            // ── Jika deadline sudah lewat → selesaikan + kembalikan stok
            if ($sisaHari <= 0) {
                $item->update(['status' => 'selesai']);
                $this->kembalikanStok($item);
                $countSelesai++;
                $this->line("  ✓ [SELESAI]   #{$item->id} {$item->nama_penyewa} (sisa: {$sisaHari} hari)");
                continue;
            }

            // ── Sisa ≤ 7 hari → segera_konfirmasi
            if ($sisaHari <= 7 && $item->status === 'berjalan') {
                $item->update(['status' => 'segera_konfirmasi']);
                $countKonfirmasi++;
                $this->line("  ⚠ [KONFIRMASI] #{$item->id} {$item->nama_penyewa} (sisa: {$sisaHari} hari)");
                continue;
            }

            // ── Sisa > 7 hari & masih segera_konfirmasi → kembalikan ke berjalan
            if ($sisaHari > 7 && $item->status === 'segera_konfirmasi') {
                $item->update(['status' => 'berjalan']);
                $countBerjalan++;
                $this->line("  ↩ [BERJALAN]  #{$item->id} {$item->nama_penyewa} (sisa: {$sisaHari} hari)");
            }
        }

        $this->newLine();
        $this->info("Sync selesai:");
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Diselesaikan otomatis',    $countSelesai],
                ['Jadi Segera Konfirmasi',   $countKonfirmasi],
                ['Kembali ke Berjalan',      $countBerjalan],
                ['Total diproses',           $aktif->count()],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Kembalikan stok semua item detail ke inventory sebagai bekas.
     */
    private function kembalikanStok(Penyewaan $penyewaan): void
    {
        foreach ($penyewaan->details as $detail) {
            if ($detail->inventory_id) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->tambahStok($detail->qty, 'bekas', true);
                }
            }
        }
    }
}