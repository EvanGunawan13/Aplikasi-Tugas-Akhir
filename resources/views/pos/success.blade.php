@extends('layouts.app')

@section('page_title', 'Transaksi Berhasil')

@section('content')
<div class="max-w-lg mx-auto text-center py-8">
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl space-y-6">
        <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>

        <div>
            <span class="text-xs font-bold text-slate-400 font-mono tracking-wider uppercase">Transaksi Sukses Diproses</span>
            <h2 class="text-2xl font-black text-slate-800 mt-1 font-mono">{{ $sale->invoice_no }}</h2>
            <p class="text-xs text-slate-500 mt-1">Stok suku cadang telah otomatis dipotong dari sistem</p>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-sm space-y-2 text-left">
            <div class="flex justify-between text-slate-600">
                <span>Pelanggan:</span>
                <strong class="text-slate-800">{{ $sale->customer_name }}</strong>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Total Belanja:</span>
                <strong class="text-slate-800 font-mono">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</strong>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Uang Diterima:</span>
                <strong class="text-slate-800 font-mono">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</strong>
            </div>
            <div class="flex justify-between text-base font-bold text-emerald-700 pt-2 border-t border-slate-200">
                <span>Kembalian:</span>
                <span class="font-mono">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tombol Aksi Cetak Nota & Transaksi Baru -->
        <div class="space-y-3 pt-2">
            <a href="{{ route('pos.receipt', $sale) }}" target="_blank" 
               class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-500/30 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>CETAK NOTA THERMAL (80mm)</span>
            </a>

            <div class="flex gap-2">
                <a href="{{ route('pos.receipt.pdf', $sale) }}" target="_blank" 
                   class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Unduh PDF</span>
                </a>
                <a href="{{ route('pos.index') }}" 
                   class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-1.5">
                    <span>+ Transaksi Baru [F2]</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
