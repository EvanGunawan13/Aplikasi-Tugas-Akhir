@extends('layouts.app')

@section('page_title', 'Rincian Pengeluaran: ' . $partnerOutput->output_no)

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 font-mono font-bold text-orange-800 bg-orange-100 rounded-xl text-base">
                    {{ $partnerOutput->output_no }}
                </span>
                <span class="text-xs text-slate-500">Tanggal: <strong>{{ $partnerOutput->output_date->format('d/m/Y') }}</strong></span>
            </div>
            <h2 class="text-xl font-bold text-slate-800 mt-2">Rekanan: {{ $partnerOutput->partnerCompany->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Biaya Antar: <strong>{{ $partnerOutput->delivery_fee > 0 ? 'Rp ' . number_format($partnerOutput->delivery_fee, 0, ',', '.') : '-' }}</strong> &bull; Dibuat Oleh: <strong>{{ $partnerOutput->creator->name }}</strong>
            </p>
            @if ($partnerOutput->notes)
                <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200 mt-2">
                    Referensi / Catatan: {{ $partnerOutput->notes }}
                </p>
            @endif
        </div>
        <div>
            <a href="{{ route('partner-outputs.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Tabel Rincian Item (Tanpa Nilai Uang Barang Sesuai Aturan Accurate) -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800 text-base">Daftar Barang Fisik yang Dikeluarkan</h3>
            <span class="text-xs text-slate-500 italic">* Nilai harga barang dicatat secara terpisah di aplikasi Accurate</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                        <th class="py-3 px-4">No.</th>
                        <th class="py-3 px-4">Kode & Nama Suku Cadang</th>
                        <th class="py-3 px-4">Part Number</th>
                        <th class="py-3 px-4 text-center">Lokasi Rak Pengambilan</th>
                        <th class="py-3 px-4 text-center">Jumlah Kuantitas Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($partnerOutput->items as $index => $item)
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
                            <td class="py-3 px-4 text-center font-extrabold text-base text-slate-800">
                                {{ $item->qty }} <span class="text-xs font-normal text-slate-500">{{ $item->product->unit->symbol ?? $item->product->unit->name }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 font-bold border-t border-slate-200">
                        <td colspan="4" class="py-3 px-4 text-right uppercase text-xs text-slate-600">Total Kuantitas Fisik:</td>
                        <td class="py-3 px-4 text-center text-base text-orange-700">{{ $partnerOutput->items->sum('qty') }} Unit</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
