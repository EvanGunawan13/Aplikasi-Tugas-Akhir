@extends('layouts.app')

@section('page_title', 'Cek Stok Fisik Gudang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Cek Posisi & Ketersediaan Stok</h2>
            <p class="text-xs text-slate-500">Monitoring real-time stok fisik seluruh barang beserta kode letak rak</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aman
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menipis
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span> Habis
            </span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('stock.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang, part number, kode..." 
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
                    <option value="">Semua Status</option>
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

    <!-- Tabel Cek Stok -->
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
                        <th class="py-3.5 px-4 text-center">Batas Min</th>
                        <th class="py-3.5 px-4 text-center">Stok Fisik</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <img src="{{ $p->primary_photo_url }}" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800 block">{{ $p->name }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $p->code }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $p->part_number ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-600 text-xs">{{ $p->category->name }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-3 py-1 text-xs font-bold font-mono rounded-lg bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $p->primary_rack_code }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center text-xs text-slate-500">
                                {{ $p->min_stock }} {{ $p->unit->symbol ?? $p->unit->name }}
                            </td>
                            <td class="py-3 px-4 text-center font-extrabold text-base {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock ? 'text-amber-600' : 'text-slate-800') }}">
                                {{ $p->stock }} <span class="text-xs font-normal text-slate-500">{{ $p->unit->symbol ?? $p->unit->name }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($p->stock <= 0)
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-700">Habis</span>
                                @elseif ($p->stock <= $p->min_stock)
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-700">Menipis</span>
                                @else
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 italic">
                                Tidak ada data stok yang sesuai filter.
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
