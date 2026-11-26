@extends('layouts.app')

@section('page_title', 'Edit Suku Cadang: ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Suku Cadang</h2>
                <p class="text-xs text-slate-500">Perbarui data barang dan unggah foto katalog tambahan</p>
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

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kode Barang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Barang (Unik) *</label>
                    <input type="text" name="code" value="{{ old('code', $product->code) }}" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono py-2.5 px-3">
                </div>

                <!-- Part Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Part Number Alat Berat</label>
                    <input type="text" name="part_number" value="{{ old('part_number', $product->part_number) }}" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono py-2.5 px-3">
                </div>

                <!-- Nama Barang -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Suku Cadang *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori *</label>
                    <select name="category_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan *</label>
                    <select name="unit_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        @foreach ($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $product->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->symbol }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Harga Beli -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Beli (Rp) *</label>
                    <input type="number" name="buy_price" value="{{ old('buy_price', $product->buy_price) }}" min="0" step="100" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Harga Jual -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Jual (Rp) *</label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" min="0" step="100" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Minimum Stok -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Batas Minimum Stok *</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center pt-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ms-2 text-xs font-semibold text-slate-700">Barang Aktif Dijual</span>
                    </label>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi & Spesifikasi</label>
                    <textarea name="description" rows="3" 
                              class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Upload Foto Tambahan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Unggah Foto Tambahan</label>
                    <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" 
                           class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-dashed border-slate-300 rounded-xl p-3">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition">
                    Hapus Barang
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('products.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                        Perbarui Suku Cadang
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" method="POST" action="{{ route('products.destroy', $product) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
