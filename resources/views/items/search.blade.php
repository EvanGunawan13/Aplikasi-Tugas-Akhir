@extends('layouts.app')

@section('page_title', 'Cari & Katalog Barang')

@section('content')
<div class="space-y-6" x-data="{ 
    modalOpen: false, 
    selectedProduct: null,
    openKatalog(product) {
        this.selectedProduct = product;
        this.modalOpen = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pencarian Cepat & Galeri Katalog</h2>
            <p class="text-xs text-slate-500">Lihat detail fisik suku cadang dan lokasi rak untuk memastikan kesesuaian barang</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('items.search') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama suku cadang, part number, atau kode barcode..." 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>
            <div class="flex gap-2">
                <select name="category_id" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow transition">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Grid Barang & Foto Katalog -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @forelse ($products as $p)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <!-- Thumbnail Foto -->
                    <div class="relative w-full h-44 bg-slate-100 overflow-hidden cursor-pointer group"
                         @click="openKatalog({
                             name: '{{ addslashes($p->name) }}',
                             code: '{{ $p->code }}',
                             part_number: '{{ $p->part_number ?? '-' }}',
                             category: '{{ $p->category->name }}',
                             rack: '{{ $p->primary_rack_code }}',
                             stock: '{{ $p->stock }} {{ $p->unit->symbol ?? $p->unit->name }}',
                             price: 'Rp {{ number_format($p->sell_price, 0, ',', '.') }}',
                             description: '{{ addslashes($p->description ?? 'Tidak ada keterangan.') }}',
                             photos: [
                                 @foreach ($p->photos as $ph)
                                     '{{ asset('storage/' . $ph->path) }}',
                                 @endforeach
                             ],
                             fallbackPhoto: '{{ $p->primary_photo_url }}'
                         })">
                        <img src="{{ $p->primary_photo_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="px-3 py-1.5 rounded-lg bg-white/90 text-slate-800 text-xs font-bold shadow flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Lihat Katalog
                            </span>
                        </div>
                        <div class="absolute top-2 left-2">
                            <span class="px-2.5 py-1 text-xs font-bold font-mono rounded-lg bg-blue-600/90 text-white backdrop-blur-sm shadow">
                                Rak: {{ $p->primary_rack_code }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">{{ $p->category->name }}</span>
                        <h3 class="font-bold text-slate-800 text-sm leading-snug line-clamp-2" title="{{ $p->name }}">{{ $p->name }}</h3>
                        <p class="text-xs text-slate-500 font-mono mt-1">Part No: {{ $p->part_number ?? '-' }}</p>
                    </div>
                </div>

                <div class="p-4 pt-0 border-t border-slate-100 flex items-center justify-between text-xs mt-2">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Stok Tersedia</span>
                        <span class="font-bold {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock ? 'text-amber-600' : 'text-slate-800') }}">
                            {{ $p->stock }} {{ $p->unit->symbol ?? $p->unit->name }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 block text-[10px]">Harga Jual</span>
                        <span class="font-extrabold text-blue-700 text-sm">
                            Rp {{ number_format($p->sell_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200">
                <p class="font-semibold text-slate-600">Tidak ada suku cadang yang cocok dengan pencarian.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $products->links() }}
    </div>

    <!-- Modal Lihat Foto dan Katalog Besar -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-blue-100 text-blue-800" x-text="selectedProduct?.code"></span>
                    <h3 class="font-bold text-slate-800 text-lg mt-1" x-text="selectedProduct?.name"></h3>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>

            <!-- Galeri Foto Besar -->
            <div class="space-y-3">
                <template x-if="selectedProduct?.photos && selectedProduct.photos.length > 0">
                    <div class="grid grid-cols-2 gap-3 max-h-80 overflow-y-auto p-1">
                        <template x-for="(photoUrl, index) in selectedProduct.photos" :key="index">
                            <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 h-48">
                                <img :src="photoUrl" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!selectedProduct?.photos || selectedProduct.photos.length === 0">
                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 h-64 flex items-center justify-center">
                        <img :src="selectedProduct?.fallbackPhoto" class="w-full h-full object-cover">
                    </div>
                </template>
            </div>

            <!-- Rincian Barang -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block">Part Number:</span>
                    <strong class="font-mono text-slate-700" x-text="selectedProduct?.part_number"></strong>
                </div>
                <div>
                    <span class="text-slate-400 block">Lokasi Rak:</span>
                    <strong class="font-mono text-blue-700" x-text="selectedProduct?.rack"></strong>
                </div>
                <div>
                    <span class="text-slate-400 block">Sisa Stok:</span>
                    <strong class="text-slate-800" x-text="selectedProduct?.stock"></strong>
                </div>
                <div>
                    <span class="text-slate-400 block">Harga Jual:</span>
                    <strong class="text-blue-700" x-text="selectedProduct?.price"></strong>
                </div>
            </div>

            <div class="text-xs text-slate-600 bg-white p-2">
                <span class="font-bold text-slate-700">Keterangan:</span>
                <p x-text="selectedProduct?.description" class="mt-0.5 text-slate-500"></p>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end">
                <button type="button" @click="modalOpen = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl">
                    Tutup Katalog
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
