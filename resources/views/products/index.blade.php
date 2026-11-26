@extends('layouts.app')

@section('page_title', 'Kelola Data Barang')

@section('content')
<div class="space-y-6">
    <!-- Header Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Katalog & Data Barang Suku Cadang</h2>
            <p class="text-xs text-slate-500">Kelola master suku cadang alat berat, penempatan rak, dan foto katalog</p>
        </div>
        <a href="{{ route('products.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Barang Baru
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, part number, kode..." 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>
            <div>
                <select name="category_id" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Status Stok</option>
                    <option value="aman" {{ $status == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="menipis" {{ $status == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="habis" {{ $status == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="flex gap-2">
                <select name="rack_id" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Rak</option>
                    @foreach ($racks as $r)
                        <option value="{{ $r->id }}" {{ $rackId == $r->id ? 'selected' : '' }}>{{ $r->code }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Data Barang -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-b border-slate-200">
                        <th class="py-3.5 px-4">Foto</th>
                        <th class="py-3.5 px-4">Kode & Nama Suku Cadang</th>
                        <th class="py-3.5 px-4">Part Number</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4 text-center">Lokasi Rak</th>
                        <th class="py-3.5 px-4 text-center">Stok</th>
                        <th class="py-3.5 px-4 text-right">Harga Beli</th>
                        <th class="py-3.5 px-4 text-right">Harga Jual</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                                    @if ($p->primaryPhoto)
                                        <img src="{{ asset('storage/' . $p->primaryPhoto->path) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('products.show', $p) }}" class="font-bold text-slate-800 hover:text-blue-600 block leading-tight">
                                    {{ $p->name }}
                                </a>
                                <span class="text-xs text-slate-400 font-mono">{{ $p->code }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono text-xs">{{ $p->part_number ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-600 text-xs">{{ $p->category->name }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $p->primary_rack_code }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="font-bold {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock ? 'text-amber-600' : 'text-slate-800') }}">
                                    {{ $p->stock }} {{ $p->unit->symbol ?? $p->unit->name }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-xs text-slate-500">
                                Rp {{ number_format($p->buy_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">
                                Rp {{ number_format($p->sell_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('products.show', $p) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('products.edit', $p) }}" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 italic">
                                Belum ada data barang suku cadang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
