@extends('layouts.app')

@section('page_title', 'Detail Suku Cadang: ' . $product->name)

@section('content')
<div class="space-y-6">
    <!-- Header Detail -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-blue-100 text-blue-800 border border-blue-200">
                    {{ $product->code }}
                </span>
                <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                    Part No: {{ $product->part_number ?? '-' }}
                </span>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mt-2">{{ $product->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">Kategori: <strong>{{ $product->category->name }}</strong> &bull; Satuan: <strong>{{ $product->unit->name }} ({{ $product->unit->symbol }})</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                Edit Suku Cadang
            </a>
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Informasi 3 Kolom: Foto & Galeri, Data Stok & Rak, Mutasi Terakhir -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom 1: Foto & Galeri Katalog -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-base">Foto & Galeri Katalog</h3>
            
            <div class="w-full h-64 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                @if ($product->primaryPhoto)
                    <img src="{{ asset('storage/' . $product->primaryPhoto->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="text-center p-6 text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-xs">Belum ada foto katalog.</p>
                    </div>
                @endif
            </div>

            <!-- Thumbnail Foto Tambahan -->
            @if ($product->photos->count() > 0)
                <div class="grid grid-cols-4 gap-2 pt-2">
                    @foreach ($product->photos as $photo)
                        <div class="relative group rounded-lg overflow-hidden border {{ $photo->is_primary ? 'border-blue-600 ring-2 ring-blue-500/20' : 'border-slate-200' }}">
                            <img src="{{ asset('storage/' . $photo->path) }}" class="w-full h-16 object-cover">
                            
                            @if (!$photo->is_primary)
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1">
                                    <form method="POST" action="{{ route('products.photos.primary', ['product' => $product, 'photo' => $photo]) }}">
                                        @csrf
                                        <button type="submit" class="p-1 bg-blue-600 text-white rounded text-[10px]" title="Jadikan Foto Utama">&#9733;</button>
                                    </form>
                                    <form method="POST" action="{{ route('products.photos.destroy', ['product' => $product, 'photo' => $photo]) }}" onsubmit="return confirm('Hapus foto ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 bg-rose-600 text-white rounded text-[10px]" title="Hapus">&times;</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Kolom 2: Detail Harga, Stok & Lokasi Rak -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-base">Harga & Penempatan Rak</h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Harga Beli:</span>
                    <span class="font-bold text-slate-700">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Harga Jual:</span>
                    <span class="font-extrabold text-blue-700 text-base">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Sisa Stok Fisik:</span>
                    <span class="font-bold {{ $product->stock <= $product->min_stock ? 'text-rose-600' : 'text-slate-800' }}">
                        {{ $product->stock }} {{ $product->unit->symbol ?? $product->unit->name }}
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Batas Minimum:</span>
                    <span class="font-medium text-slate-600">{{ $product->min_stock }} {{ $product->unit->symbol ?? $product->unit->name }}</span>
                </div>
            </div>

            <div class="pt-2">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lokasi Rak Gudang</h4>
                <div class="space-y-2">
                    @forelse ($product->productRacks as $pr)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-blue-800 text-sm">{{ $pr->rack->code }}</span>
                                <p class="text-xs text-slate-600 font-medium">{{ $pr->rack->name }} ({{ $pr->rack->zone }})</p>
                            </div>
                            @if ($pr->is_primary)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">Rak Utama</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-slate-200 text-slate-600">Sekunder</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ditempatkan di rak gudang manapun.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-2">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Deskripsi</h4>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $product->description ?? 'Tidak ada catatan deskripsi.' }}</p>
            </div>
        </div>

        <!-- Kolom 3: Riwayat Kartu Stok Terakhir -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-base">Riwayat Kartu Stok Terakhir</h3>

            <div class="overflow-y-auto max-h-96 space-y-3">
                @forelse ($product->stockMovements as $move)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs flex items-start justify-between">
                        <div>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ $move->type === 'sale' ? 'bg-emerald-100 text-emerald-800' : ($move->type === 'purchase' ? 'bg-blue-100 text-blue-800' : ($move->type === 'partner_out' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800')) }}">
                                {{ $move->type }}
                            </span>
                            <p class="text-slate-600 text-[11px] mt-1">{{ $move->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-[10px] text-slate-400">Oleh: {{ $move->creator->name ?? 'Sistem' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-sm {{ $move->qty_change > 0 ? 'text-blue-600' : 'text-rose-600' }}">
                                {{ $move->qty_change > 0 ? '+' : '' }}{{ $move->qty_change }}
                            </span>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $move->stock_before }} &rarr; <strong>{{ $move->stock_after }}</strong></p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Belum ada riwayat pergerakan stok.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
