@extends('layouts.app')

@section('page_title', 'Isi Rak: ' . $rack->code)

@section('content')
<div class="space-y-6">
    <!-- Header Informasi Rak -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 font-mono font-bold text-lg rounded-xl border border-blue-200">
                    {{ $rack->code }}
                </span>
                <h2 class="text-xl font-bold text-slate-800">{{ $rack->name }}</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Zona: <strong>{{ $rack->zone ?? '-' }}</strong> &bull; {{ $rack->description ?? 'Tidak ada keterangan khusus.' }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('product-racks.index', ['rack_id' => $rack->id]) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition">
                + Tempatkan Barang ke Rak Ini
            </a>
            <a href="{{ route('racks.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Tabel Barang dalam Rak -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 text-base mb-4">
            Daftar Suku Cadang di Rak Ini ({{ $rack->products->count() }} Item)
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                        <th class="py-3 px-4">Foto</th>
                        <th class="py-3 px-4">Kode & Nama Barang</th>
                        <th class="py-3 px-4">Part Number</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Stok</th>
                        <th class="py-3 px-4 text-center">Tipe Rak</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rack->products as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <img src="{{ $p->primary_photo_url }}" alt="{{ $p->name }}" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('products.show', $p) }}" class="font-bold text-blue-600 hover:underline block">
                                    {{ $p->name }}
                                </a>
                                <span class="text-xs text-slate-400 font-mono">{{ $p->code }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $p->part_number ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $p->category->name }}</td>
                            <td class="py-3 px-4 text-center font-bold {{ $p->stock <= 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $p->stock }} {{ $p->unit->symbol ?? $p->unit->name }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($p->pivot->is_primary)
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">Rak Utama</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-slate-100 text-slate-600">Sekunder</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">
                                Rp {{ number_format($p->sell_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="{{ route('product-racks.destroy', ['product' => $p->id, 'rack' => $rack->id]) }}" onsubmit="return confirm('Lepas penempatan barang ini dari rak?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded transition font-medium">
                                        Lepas
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 italic">
                                Belum ada suku cadang yang ditempatkan di rak ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
