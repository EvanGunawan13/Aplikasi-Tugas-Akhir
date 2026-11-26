@extends('layouts.app')

@section('page_title', 'Catat Pembelian Pemasok')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Riwayat Pembelian dari Pemasok</h2>
            <p class="text-xs text-slate-500">Pencatatan faktur barang masuk (stok bertambah otomatis setelah validasi fisik gudang)</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Catat Pembelian Baru
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor PO atau surat jalan vendor..." 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>
            <div class="flex gap-2">
                <select name="supplier_id" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Pemasok</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Pembelian -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-b border-slate-200">
                        <th class="py-3.5 px-4">No. Pembelian</th>
                        <th class="py-3.5 px-4">Tanggal Masuk</th>
                        <th class="py-3.5 px-4">Pemasok (Vendor)</th>
                        <th class="py-3.5 px-4">No. Surat Jalan Vendor</th>
                        <th class="py-3.5 px-4">Divalidasi Fisik Oleh</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Item</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($purchases as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-700">
                                <a href="{{ route('purchases.show', $p) }}" class="hover:underline">
                                    {{ $p->purchase_no }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $p->purchase_date->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">{{ $p->supplier->name }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-500">{{ $p->supplier_doc_no ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    {{ $p->validated_by_warehouse }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 text-slate-700">
                                    {{ $p->items->count() }} Macam
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('purchases.show', $p) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                    Rincian
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                Belum ada transaksi pembelian barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
