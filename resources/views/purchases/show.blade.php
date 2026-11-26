@extends('layouts.app')

@section('page_title', 'Rincian Pembelian: ' . $purchase->purchase_no)

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 font-mono font-bold text-blue-800 bg-blue-100 rounded-xl text-base">
                    {{ $purchase->purchase_no }}
                </span>
                <span class="text-xs text-slate-500">Tanggal: <strong>{{ $purchase->purchase_date->format('d/m/Y') }}</strong></span>
            </div>
            <h2 class="text-xl font-bold text-slate-800 mt-2">Pemasok: {{ $purchase->supplier->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                No. Surat Jalan Vendor: <strong>{{ $purchase->supplier_doc_no ?? '-' }}</strong> &bull; Divalidasi Gudang: <strong>{{ $purchase->validated_by_warehouse }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Tabel Rincian Item -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 text-base mb-4">Daftar Barang Masuk</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                        <th class="py-3 px-4">No.</th>
                        <th class="py-3 px-4">Kode & Nama Suku Cadang</th>
                        <th class="py-3 px-4">Part Number</th>
                        <th class="py-3 px-4 text-center">Lokasi Rak</th>
                        <th class="py-3 px-4 text-center">Kuantitas Masuk</th>
                        <th class="py-3 px-4 text-right">Harga Beli Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $grandTotal = 0; @endphp
                    @foreach ($purchase->items as $index => $item)
                        @php 
                            $subtotal = $item->qty * $item->buy_price; 
                            $grandTotal += $subtotal;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800 block">{{ $item->product->name }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $item->product->code }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $item->product->part_number ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 text-xs font-mono font-bold bg-blue-50 text-blue-700 rounded border border-blue-200">
                                    {{ $item->product->primary_rack_code }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ $item->qty }} {{ $item->product->unit->symbol ?? $item->product->unit->name }}
                            </td>
                            <td class="py-3 px-4 text-right text-xs font-mono text-slate-600">
                                Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 font-bold border-t border-slate-200">
                        <td colspan="6" class="py-3 px-4 text-right uppercase text-xs text-slate-600">Total Nilai Pembelian:</td>
                        <td class="py-3 px-4 text-right text-base text-blue-800">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
