@extends('layouts.app')

@section('page_title', 'Kelola Data Rak Gudang')

@section('content')
<div class="space-y-6">
    <!-- Header Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Lokasi Rak Gudang</h2>
            <p class="text-xs text-slate-500">Pemetaan zona dan kode rak penyimpanan suku cadang CV Mandiri Diesel</p>
        </div>
        <a href="{{ route('racks.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Rak Baru
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('racks.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode rak, nama rak, atau keterangan..." 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>
            <div class="flex gap-2">
                <select name="zone" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Zona</option>
                    @foreach ($zones as $z)
                        <option value="{{ $z }}" {{ $zone == $z ? 'selected' : '' }}>{{ $z }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Grid Rak -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($racks as $r)
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div>
                            <span class="inline-block px-3 py-1 text-sm font-black rounded-lg bg-blue-100 text-blue-800 font-mono tracking-wide border border-blue-200">
                                {{ $r->code }}
                            </span>
                            <h3 class="font-bold text-slate-800 text-base mt-2">{{ $r->name }}</h3>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $r->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $r->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <p class="text-xs text-blue-600 font-semibold mb-2">{{ $r->zone ?? 'Tanpa Zona' }}</p>
                    <p class="text-xs text-slate-500 mb-4">{{ $r->description ?? 'Tidak ada keterangan khusus.' }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-600">
                        <strong>{{ $r->products_count }}</strong> Suku Cadang
                    </span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('racks.show', $r) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                            Lihat Isi
                        </a>
                        <a href="{{ route('racks.edit', $r) }}" class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-medium transition">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 00-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <p class="font-semibold text-slate-600">Tidak ada data rak ditemukan.</p>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian lain.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $racks->links() }}
    </div>
</div>
@endsection
