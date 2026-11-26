@extends('layouts.app')

@section('page_title', 'Input Pembelian Pemasok')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
    items: [{ product_id: '', qty: 1, buy_price: 0 }],
    addItem() {
        this.items.push({ product_id: '', qty: 1, buy_price: 0 });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    }
}">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Catat Faktur Pembelian Pemasok</h2>
                <p class="text-xs text-slate-500">Stok barang akan otomatis bertambah setelah disimpan</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
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

        <form method="POST" action="{{ route('purchases.store') }}" class="space-y-6">
            @csrf

            <!-- Header Pembelian -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pemasok (Vendor) *</label>
                    <select name="supplier_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">Pilih Pemasok...</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Penerimaan *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Surat Jalan / Faktur Pemasok</label>
                    <input type="text" name="supplier_doc_no" value="{{ old('supplier_doc_no') }}" placeholder="Contoh: SJ-UT-202611-09" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <!-- Checkbox / Nama Validasi Petugas Gudang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Divalidasi Fisik Oleh Petugas Gudang *</label>
                    <input type="text" name="validated_by_warehouse" value="{{ old('validated_by_warehouse', 'Budi Santoso (Petugas Gudang)') }}" required 
                           placeholder="Nama petugas gudang yang memeriksa fisik..." 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" placeholder="Keterangan pengiriman, kondisi kemasan, armada ekspedisi..." 
                              class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Daftar Item Pembelian Dinamis -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Rincian Barang yang Masuk</h3>
                    <button type="button" @click="addItem()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-lg transition flex items-center gap-1">
                        + Tambah Baris Barang
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <div class="flex-1">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Pilih Suku Cadang</label>
                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" required 
                                        class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-2.5">
                                    <option value="">Pilih Barang...</option>
                                    @foreach ($products as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->code }} - {{ $pr->name }} (Stok Saat Ini: {{ $pr->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-28">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Qty Masuk</label>
                                <input type="number" :name="'items[' + index + '][qty]'" x-model="item.qty" min="1" required 
                                       class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-2.5">
                            </div>
                            <div class="w-36">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Harga Beli Satuan</label>
                                <input type="number" :name="'items[' + index + '][buy_price]'" x-model="item.buy_price" min="0" step="100" 
                                       placeholder="Rp 0" 
                                       class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-2.5">
                            </div>
                            <div class="pt-5">
                                <button type="button" @click="removeItem(index)" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg" title="Hapus Baris">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Konfirmasi Validasi Fisik -->
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3">
                <input type="checkbox" id="confirm_check" required class="mt-0.5 rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                <label for="confirm_check" class="text-xs text-blue-900 leading-snug cursor-pointer font-medium">
                    Saya menyatakan bahwa fisik seluruh barang dalam daftar di atas telah diperiksa, dihitung, dan diverifikasi kondisinya oleh Petugas Gudang sebelum stok ditambahkan ke sistem.
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('purchases.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                    Simpan & Perbarui Stok
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
