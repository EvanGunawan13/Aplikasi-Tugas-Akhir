@extends('layouts.app')

@section('page_title', 'Barang Keluar Rekanan B2B')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Riwayat Pengeluaran Barang ke Rekanan (B2B)</h2>
            <p class="text-xs text-slate-500">Mencatat mutasi fisik keluar tanpa nilai akuntansi (untuk dicocokkan dengan sistem Accurate)</p>
        </div>
        <a href="{{ route('partner-outputs.create') }}" class="px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Catat Pengeluaran Rekanan
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('partner-outputs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor pengeluaran atau catatan..." 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>
            <div class="flex gap-2">
                <select name="partner_company_id" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Perusahaan Rekanan</option>
                    @foreach ($partners as $p)
                        <option value="{{ $p->id }}" {{ $partnerId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Barang Keluar Rekanan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-b border-slate-200">
                        <th class="py-3.5 px-4">No. Pengeluaran</th>
                        <th class="py-3.5 px-4">Tanggal Keluar</th>
                        <th class="py-3.5 px-4">Perusahaan Rekanan (B2B)</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Item</th>
                        <th class="py-3.5 px-4 text-center">Total Fisik (Qty)</th>
                        <th class="py-3.5 px-4 text-right">Biaya Antar</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($outputs as $out)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-orange-700">
                                <a href="{{ route('partner-outputs.show', $out) }}" class="hover:underline">
                                    {{ $out->output_no }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $out->output_date->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">{{ $out->partnerCompany->name }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 text-slate-700">
                                    {{ $out->items->count() }} Macam
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-extrabold text-slate-800">
                                {{ $out->items->sum('qty') }} Pcs/Unit
                            </td>
                            <td class="py-3.5 px-4 text-right text-xs text-slate-600">
                                {{ $out->delivery_fee > 0 ? 'Rp ' . number_format($out->delivery_fee, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('partner-outputs.show', $out) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                    Rincian
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                Belum ada transaksi pengeluaran barang ke rekanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $outputs->links() }}
        </div>
    </div>
</div>
@endsection
