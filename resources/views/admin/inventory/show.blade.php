<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Inventory — {{ $inventory->nama_produk }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        {{-- Info Produk --}}
        <div class="bg-white rounded-xl shadow p-5 mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase">Kategori</p>
                <p class="font-semibold text-gray-800 mt-1">{{ $inventory->kategori ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Stok Tersedia</p>
                <p class="font-semibold text-green-600 mt-1 text-xl">{{ $inventory->stok_tersedia }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Sedang Disewa</p>
                <p class="font-semibold text-orange-500 mt-1 text-xl">{{ $inventory->stok_disewa }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Total Stok</p>
                <p class="font-semibold text-gray-800 mt-1 text-xl">{{ $inventory->total_stok }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Stok Baru</p>
                <p class="font-semibold text-blue-600 mt-1">{{ $inventory->stok_baru }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Stok Bekas</p>
                <p class="font-semibold text-purple-600 mt-1">{{ $inventory->stok_bekas }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Harga Beli Terakhir</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $inventory->harga_beli_terakhir
                        ? 'Rp ' . number_format($inventory->harga_beli_terakhir, 0, ',', '.')
                        : '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Satuan</p>
                <p class="font-semibold text-gray-800 mt-1">{{ $inventory->satuan }}</p>
            </div>
        </div>

        {{-- Log Riwayat --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <h3 class="font-semibold text-gray-700 text-lg">Riwayat Pergerakan Stok</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Tipe Transaksi</th>
                            <th class="px-4 py-3 text-center">Perubahan Qty</th>
                            <th class="px-4 py-3">Kondisi</th>
                            <th class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $log->reference_type_color }}">
                                        {{ $log->reference_type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold
                                    {{ $log->qty_change > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $log->qty_change > 0 ? '+' : '' }}{{ $log->qty_change }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $log->kondisi === 'bekas' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($log->kondisi) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $log->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                                    Belum ada riwayat pergerakan stok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="p-4 border-t">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>