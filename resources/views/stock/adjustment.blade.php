@extends('layouts.app')

@section('page_title', 'Koreksi Stok Fisik')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Koreksi Stok Fisik (Stock Adjustment)</h2>
            <p class="text-xs text-slate-500">Penyesuaian stok fisik hasil opname dengan alasan wajib dan jejak audit lengkap</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Koreksi Stok -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm h-fit">
            <h3 class="font-bold text-slate-800 text-base mb-4">Form Koreksi Stok</h3>

            <form method="POST" action="{{ route('stock.adjustment.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Suku Cadang *</label>
                    <select name="product_id" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">Pilih Suku Cadang...</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ $p->name }} (Stok Sistem: {{ $p->stock }} {{ $p->unit->symbol ?? $p->unit->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah Stok Fisik Sebenarnya *</label>
                    <input type="number" name="stock_after" value="{{ old('stock_after') }}" min="0" required placeholder="Hasil hitung fisik..." 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <p class="text-[11px] text-slate-400 mt-1">Stok sistem akan otomatis diubah menjadi nilai ini.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan Penyesuaian (Wajib Diisi) *</label>
                    <textarea name="reason" rows="3" required placeholder="Contoh: Selisih opname akhir bulan, barang rusak/pecah saat pemindahan, dll." 
                              class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">{{ old('reason') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                        Simpan Koreksi Stok
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat Koreksi -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 text-base mb-4">Riwayat Koreksi Stok Terakhir</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                            <th class="py-3 px-3">Tanggal & Waktu</th>
                            <th class="py-3 px-3">Suku Cadang</th>
                            <th class="py-3 px-3 text-center">Stok Awal</th>
                            <th class="py-3 px-3 text-center">Stok Akhir</th>
                            <th class="py-3 px-3 text-center">Selisih</th>
                            <th class="py-3 px-3">Alasan</th>
                            <th class="py-3 px-3">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($adjustments as $adj)
                            <tr class="hover:bg-slate-50 transition text-xs">
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">
                                    {{ $adj->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-bold text-slate-800 block">{{ $adj->product->name }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $adj->product->code }}</span>
                                </td>
                                <td class="py-3 px-3 text-center font-medium text-slate-600">
                                    {{ $adj->stock_before }}
                                </td>
                                <td class="py-3 px-3 text-center font-bold text-slate-800">
                                    {{ $adj->stock_after }}
                                </td>
                                <td class="py-3 px-3 text-center font-extrabold {{ $adj->difference >= 0 ? 'text-blue-600' : 'text-rose-600' }}">
                                    {{ $adj->difference >= 0 ? '+' : '' }}{{ $adj->difference }}
                                </td>
                                <td class="py-3 px-3 text-slate-700 max-w-xs">
                                    {{ $adj->reason }}
                                </td>
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">
                                    {{ $adj->creator->name ?? 'Gudang' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                    Belum ada data riwayat koreksi stok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $adjustments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
