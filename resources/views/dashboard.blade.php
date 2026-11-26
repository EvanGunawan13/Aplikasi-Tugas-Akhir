@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Greeting -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-blue-100 text-sm mt-1">CV Mandiri Diesel &bull; Sistem POS & Pengelolaan Persediaan Berbasis Rak Gudang</p>
        </div>
        <div class="flex items-center gap-3">
            @if (in_array(auth()->user()->role, ['kasir', 'admin']))
                <a href="{{ route('pos.index') }}" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 font-bold text-sm rounded-xl shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Buka Kasir (POS)
                </a>
            @endif
            @if (in_array(auth()->user()->role, ['pemilik', 'admin']))
                <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-white/20 hover:bg-white/30 font-semibold text-sm rounded-xl backdrop-blur-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Rekap Transaksi
                </a>
            @endif
        </div>
    </div>

    <!-- Metrik Kartu -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Penjualan Hari Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penjualan Hari Ini</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">Rp {{ number_format($todaySalesAmount, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $todaySalesCount }} transaksi selesai</p>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Barang Stok Menipis</p>
                <h3 class="text-xl font-bold text-rose-600 mt-0.5">{{ $lowStockCount }} Suku Cadang</h3>
                <p class="text-xs text-slate-500 mt-0.5">&le; batas minimum stok</p>
            </div>
        </div>

        <!-- Total Suku Cadang -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Katalog Barang</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $totalProducts }} Item</h3>
                <p class="text-xs text-slate-500 mt-0.5">Semua kategori</p>
            </div>
        </div>

        <!-- Total Rak Gudang -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Lokasi Rak Gudang</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $totalRacks }} Rak Aktif</h3>
                <p class="text-xs text-slate-500 mt-0.5">Zona A, B, C, D, E, F</p>
            </div>
        </div>
    </div>

    <!-- Grafik & Stok Kritis -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Grafik Penjualan 7 Hari Terakhir -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-base">Tren Penjualan (7 Hari Terakhir)</h3>
                <span class="text-xs font-medium text-slate-400">Total Transaksi B2C Selesai</span>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- 5 Aktivitas Terakhir -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col">
            <h3 class="font-bold text-slate-800 text-base mb-4">Aktivitas Terakhir</h3>
            <div class="space-y-4 flex-1">
                @forelse ($recentActivities as $act)
                    <div class="flex items-start gap-3 text-xs border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">
                            {{ $act->user ? substr($act->user->name, 0, 1) : 'S' }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-slate-700 leading-tight">{{ $act->description ?? $act->action }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $act->user->name ?? 'Sistem' }} &bull; {{ $act->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Belum ada aktivitas tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tabel Peringatan Stok Menipis -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Peringatan Suku Cadang Perlu Restock</h3>
                <p class="text-xs text-slate-500">Barang dengan stok kurang dari atau sama dengan batas minimum</p>
            </div>
            @if (auth()->user()->role === 'petugas_gudang')
                <a href="{{ route('stock.index', ['status' => 'menipis']) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    Lihat Semua di Gudang &rarr;
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                        <th class="py-3 px-4">Kode & Nama Barang</th>
                        <th class="py-3 px-4">Part Number</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Lokasi Rak</th>
                        <th class="py-3 px-4 text-center">Sisa Stok</th>
                        <th class="py-3 px-4 text-center">Batas Min</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($lowStockProducts as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800 block">{{ $p->name }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $p->code }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono text-xs">{{ $p->part_number ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $p->category->name }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $p->primary_rack_code }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold {{ $p->stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                {{ $p->stock }} {{ $p->unit->symbol ?? $p->unit->name }}
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500 font-medium">
                                {{ $p->min_stock }} {{ $p->unit->symbol ?? $p->unit->name }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($p->stock <= 0)
                                    <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded-full bg-rose-100 text-rose-700">Habis</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded-full bg-amber-100 text-amber-700">Menipis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400 italic">Semua stok barang dalam kondisi aman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($salesChartLabels) !!},
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: {!! json_encode($salesChartData) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#1d4ed8',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
