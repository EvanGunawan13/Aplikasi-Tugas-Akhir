@extends('layouts.app')

@section('page_title', 'Tambah Suku Cadang')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Tambah Suku Cadang Baru</h2>
                <p class="text-xs text-slate-500">Input data barang, letak rak utama, dan foto katalog</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kode Barang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Barang (Unik / Barcode) *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Contoh: FLT-OIL-PC200" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono py-2.5 px-3">
                </div>

                <!-- Part Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Part Number Alat Berat</label>
                    <input type="text" name="part_number" value="{{ old('part_number') }}" placeholder="Contoh: 6736-51-5142, 1R-0716" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono py-2.5 px-3">
                </div>

                <!-- Nama Barang -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Suku Cadang *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Oil Filter Mesin Komatsu PC200-8" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori *</label>
                    <select name="category_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">Pilih Kategori...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan *</label>
                    <select name="unit_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">Pilih Satuan...</option>
                        @foreach ($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->symbol }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Harga Beli -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Beli (Rp) *</label>
                    <input type="number" name="buy_price" value="{{ old('buy_price', 0) }}" min="0" step="100" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Harga Jual -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Jual (Rp) *</label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', 0) }}" min="0" step="100" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Stok Awal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Stok Awal Fisik *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Minimum Stok -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Batas Minimum Stok (Alert) *</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" min="0" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Penempatan Rak Utama -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Penempatan Rak Utama</label>
                    <select name="rack_id" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">Pilih Rak Penyimpanan...</option>
                        @foreach ($racks as $r)
                            <option value="{{ $r->id }}" {{ old('rack_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->code }} - {{ $r->name }} ({{ $r->zone ?? 'Tanpa Zona' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi & Spesifikasi</label>
                    <textarea name="description" rows="3" placeholder="Informasi spesifikasi teknis atau kompatibilitas armada mesin..." 
                              class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">{{ old('description') }}</textarea>
                </div>

                <!-- Unggah Foto & Katalog -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Unggah Foto Katalog (Bisa Lebih Dari Satu)</label>
                    <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" 
                           class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-dashed border-slate-300 rounded-xl p-3">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 2 MB per foto.</p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                    Simpan Suku Cadang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
