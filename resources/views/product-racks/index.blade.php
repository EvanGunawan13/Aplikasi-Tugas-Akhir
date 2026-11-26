@extends('layouts.app')

@section('page_title', 'Tempatkan Barang ke Rak')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Penempatan Barang ke Rak Gudang</h2>
            <p class="text-xs text-slate-500">Pilih rak gudang dan tempatkan barang agar letak fisik tercatat akurat</p>
        </div>
    </div>

    <!-- Layout 2 Kolom: Kiri Form Penempatan, Kanan Inspeksi Isi Rak -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Penempatan Barang -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm h-fit">
            <h3 class="font-bold text-slate-800 text-base mb-4">Tempatkan Barang Baru ke Rak</h3>

            <form method="POST" action="{{ route('product-racks.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Suku Cadang *</label>
                    <select name="product_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">Cari dan Pilih Suku Cadang...</option>
                        @foreach ($allProducts as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Rak Gudang *</label>
                    <select name="rack_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">Pilih Rak...</option>
                        @foreach ($racks as $r)
                            <option value="{{ $r->id }}" {{ $rackId == $r->id ? 'selected' : '' }}>
                                {{ $r->code }} - {{ $r->name }} ({{ $r->zone ?? 'Tanpa Zona' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Letak (Opsional)</label>
                    <input type="text" name="note" placeholder="Contoh: Baris ke-2, sisi kanan" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>

                <div class="flex items-center">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_primary" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ms-2 text-xs font-semibold text-slate-700">Jadikan Sebagai Rak Utama</span>
                    </label>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                        Simpan Penempatan
                    </button>
                </div>
            </form>
        </div>

        <!-- Tampilan Isi Rak Terpilih -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
                <form method="GET" action="{{ route('product-racks.index') }}" class="flex items-center gap-3 w-full">
                    <label class="text-xs font-bold text-slate-700 uppercase whitespace-nowrap">Lihat Isi Rak:</label>
                    <select name="rack_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                        <option value="">-- Pilih Rak Gudang --</option>
                        @foreach ($racks as $r)
                            <option value="{{ $r->id }}" {{ $rackId == $r->id ? 'selected' : '' }}>
                                Rak {{ $r->code }} ({{ $r->name }}) - {{ $r->products_count }} item
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if ($selectedRack)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div>
                            <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-blue-100 text-blue-800">
                                {{ $selectedRack->code }}
                            </span>
                            <h3 class="text-lg font-bold text-slate-800 mt-1">{{ $selectedRack->name }}</h3>
                            <p class="text-xs text-slate-500">Zona: {{ $selectedRack->zone ?? '-' }}</p>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
                            {{ $productsInRack->count() }} Suku Cadang Ditempatkan
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($productsInRack as $p)
                            <div class="py-3 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $p->primary_photo_url }}" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-800 leading-tight">{{ $p->name }}</h4>
                                        <p class="text-xs text-slate-400 font-mono">{{ $p->code }} &bull; Part: {{ $p->part_number ?? '-' }}</p>
                                        @if ($p->pivot->note)
                                            <p class="text-[11px] text-amber-600 mt-0.5">Catatan: {{ $p->pivot->note }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-right">
                                    <div>
                                        <span class="text-xs font-bold block {{ $p->stock <= 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                            Stok: {{ $p->stock }} {{ $p->unit->symbol ?? $p->unit->name }}
                                        </span>
                                        @if ($p->pivot->is_primary)
                                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Utama</span>
                                        @else
                                            <span class="text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Sekunder</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('product-racks.destroy', ['product' => $p->id, 'rack' => $selectedRack->id]) }}" onsubmit="return confirm('Lepas barang dari rak ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700" title="Lepas dari Rak">
                                            &times;
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic py-6 text-center">Belum ada barang di rak ini.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200">
                    <p class="font-semibold text-slate-600">Pilih salah satu rak di atas untuk melihat isi barangnya.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
