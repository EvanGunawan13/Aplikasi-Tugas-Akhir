@extends('layouts.app')

@section('page_title', 'Rekapitulasi Transaksi per Periode')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Rekapitulasi Arus Transaksi & Barang</h2>
            <p class="text-xs text-slate-500">Rekapitulasi 3 arus barang: Penjualan Eceran (B2C), Keluar Rekanan (B2B), dan Penerimaan Pemasok</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'type' => $selected_type, 'action' => 'stream']) }}" target="_blank" 
               class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span>Cetak / Pratinjau PDF</span>
            </a>
            <a href="{{ route('reports.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'type' => $selected_type, 'action' => 'download']) }}" 
               class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Unduh Laporan (PDF)</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar Presets & Custom Date -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-4">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-3">
            <!-- Preset Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase mr-2">Preset Periode:</span>
                <button type="submit" name="preset" value="today" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $preset === 'today' ? 'bg-blue-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Hari Ini
                </button>
                <button type="submit" name="preset" value="7_days" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $preset === '7_days' ? 'bg-blue-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    7 Hari Terakhir
                </button>
                <button type="submit" name="preset" value="this_month" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $preset === 'this_month' ? 'bg-blue-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Bulan Ini
                </button>
                <button type="submit" name="preset" value="last_month" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $preset === 'last_month' ? 'bg-blue-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Bulan Lalu
                </button>
            </div>

            <!-- Custom Date & Transaction Type -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $start_date }}" 
                           class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $end_date }}" 
                           class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jenis Transaksi</label>
                    <select name="type" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                        <option value="all" {{ $selected_type === 'all' ? 'selected' : '' }}>Semua Jenis Transaksi</option>
                        <option value="sale" {{ $selected_type === 'sale' ? 'selected' : '' }}>Penjualan Eceran (B2C)</option>
                        <option value="partner_out" {{ $selected_type === 'partner_out' ? 'selected' : '' }}>Barang Keluar Rekanan (B2B)</option>
                        <option value="purchase" {{ $selected_type === 'purchase' ? 'selected' : '' }}>Penerimaan dari Pemasok</option>
                    </select>
                </div>
                <div class="pt-5 flex items-end">
                    <input type="hidden" name="preset" value="custom">
                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Ringkasan Metrik Periode -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Penjualan B2C</span>
            <h3 class="text-xl font-black text-blue-900 mt-1">Rp {{ number_format($summary['total_sales_amount'], 0, ',', '.') }}</h3>
            <span class="text-xs text-slate-500 mt-0.5 block">{{ $summary['total_sales_count'] }} transaksi</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Jumlah Transaksi POS</span>
            <h3 class="text-xl font-black text-slate-800 mt-1">{{ $summary['total_sales_count'] }} Nota</h3>
            <span class="text-xs text-slate-500 mt-0.5 block">Selesai & Lunas</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Barang Keluar Rekanan B2B</span>
            <h3 class="text-xl font-black text-orange-600 mt-1">{{ $summary['total_b2b_qty'] }} Unit</h3>
            <span class="text-xs text-slate-500 mt-0.5 block">Kuantitas fisik keluar</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Penerimaan Pemasok</span>
            <h3 class="text-xl font-black text-emerald-600 mt-1">{{ $summary['total_purchase_qty'] }} Unit</h3>
            <span class="text-xs text-slate-500 mt-0.5 block">Kuantitas fisik masuk</span>
        </div>
    </div>

    <!-- Bagian 1: Penjualan Eceran (B2C) -->
    @if ($selected_type === 'all' || $selected_type === 'sale')
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">1. Penjualan Eceran (B2C)</h3>
                    <p class="text-xs text-slate-500">Rincian faktur POS per kasir beserta subtotal dan ongkir</p>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-lg">
                    Total: Rp {{ number_format($sales->sum('grand_total'), 0, ',', '.') }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">No. Nota</th>
                            <th class="py-3 px-3">Pelanggan</th>
                            <th class="py-3 px-3">Rincian Suku Cadang</th>
                            <th class="py-3 px-3 text-center">Metode</th>
                            <th class="py-3 px-3 text-right">Ongkir</th>
                            <th class="py-3 px-3 text-right">Total Transaksi</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-slate-50 transition text-xs">
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-3 font-mono font-bold text-blue-700 whitespace-nowrap">{{ $sale->invoice_no }}</td>
                                <td class="py-3 px-3 font-medium text-slate-800">{{ $sale->customer_name }}</td>
                                <td class="py-3 px-3">
                                    <ul class="space-y-1">
                                        @foreach ($sale->items as $it)
                                            <li>
                                                <strong>{{ $it->qty }}x</strong> {{ $it->product_name_snapshot }} 
                                                <span class="text-slate-400 font-mono">({{ $it->product_code_snapshot }})</span>
                                                <span class="text-slate-500">&bull; Rak: {{ $it->product->primary_rack_code }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3 px-3 text-center uppercase font-bold text-[10px] text-slate-600">{{ $sale->payment_method }}</td>
                                <td class="py-3 px-3 text-right text-slate-500">
                                    {{ $sale->delivery_fee > 0 ? 'Rp ' . number_format($sale->delivery_fee, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold text-blue-900">
                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('pos.receipt', $sale) }}" target="_blank" class="p-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-600" title="Cetak Nota">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        @if (auth()->user()->role === 'admin' && $sale->status === 'completed')
                                            <button type="button" onclick="voidSaleModal('{{ $sale->id }}', '{{ $sale->invoice_no }}')" class="p-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-600" title="Batalkan (Void)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 italic">Tidak ada transaksi penjualan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Bagian 2: Barang Keluar Rekanan (B2B) -->
    @if ($selected_type === 'all' || $selected_type === 'partner_out')
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">2. Barang Keluar Rekanan (B2B)</h3>
                    <p class="text-xs text-slate-500">Mutasi fisik barang untuk pencocokan Accurate (tanpa nilai nominal transaksi)</p>
                </div>
                <span class="text-xs font-bold text-orange-700 bg-orange-50 px-3 py-1 rounded-lg">
                    Total Kuantitas: {{ $partner_outputs->flatMap->items->sum('qty') }} Unit
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">No. Pengeluaran</th>
                            <th class="py-3 px-3">Perusahaan Rekanan</th>
                            <th class="py-3 px-3">Daftar Suku Cadang</th>
                            <th class="py-3 px-3 text-center">Total Kuantitas</th>
                            <th class="py-3 px-3">Catatan / DO Accurate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($partner_outputs as $out)
                            <tr class="hover:bg-slate-50 transition text-xs">
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $out->output_date->format('d/m/Y') }}</td>
                                <td class="py-3 px-3 font-mono font-bold text-orange-700 whitespace-nowrap">{{ $out->output_no }}</td>
                                <td class="py-3 px-3 font-medium text-slate-800">{{ $out->partnerCompany->name }}</td>
                                <td class="py-3 px-3">
                                    <ul class="space-y-1">
                                        @foreach ($out->items as $it)
                                            <li>
                                                <strong>{{ $it->qty }}x</strong> {{ $it->product->name }} 
                                                <span class="text-slate-400 font-mono">({{ $it->product->code }})</span>
                                                <span class="text-slate-500">&bull; Rak: {{ $it->product->primary_rack_code }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3 px-3 text-center font-extrabold text-slate-800">
                                    {{ $out->items->sum('qty') }} Unit
                                </td>
                                <td class="py-3 px-3 text-slate-600 max-w-xs truncate">{{ $out->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 italic">Tidak ada pengeluaran barang rekanan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Bagian 3: Penerimaan dari Pemasok -->
    @if ($selected_type === 'all' || $selected_type === 'purchase')
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">3. Penerimaan Suku Cadang dari Pemasok</h3>
                    <p class="text-xs text-slate-500">Barang masuk yang telah diverifikasi fisik oleh Petugas Gudang</p>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg">
                    Total Kuantitas: {{ $purchases->flatMap->items->sum('qty') }} Unit
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-y border-slate-200">
                            <th class="py-3 px-3">Tanggal Masuk</th>
                            <th class="py-3 px-3">No. Pembelian</th>
                            <th class="py-3 px-3">Nama Pemasok</th>
                            <th class="py-3 px-3">No. Surat Jalan Vendor</th>
                            <th class="py-3 px-3">Suku Cadang yang Diterima</th>
                            <th class="py-3 px-3 text-center">Total Masuk</th>
                            <th class="py-3 px-3">Verifikasi Gudang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($purchases as $p)
                            <tr class="hover:bg-slate-50 transition text-xs">
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $p->purchase_date->format('d/m/Y') }}</td>
                                <td class="py-3 px-3 font-mono font-bold text-blue-700 whitespace-nowrap">{{ $p->purchase_no }}</td>
                                <td class="py-3 px-3 font-medium text-slate-800">{{ $p->supplier->name }}</td>
                                <td class="py-3 px-3 font-mono text-slate-500">{{ $p->supplier_doc_no ?? '-' }}</td>
                                <td class="py-3 px-3">
                                    <ul class="space-y-1">
                                        @foreach ($p->items as $it)
                                            <li>
                                                <strong>{{ $it->qty }}x</strong> {{ $it->product->name }} 
                                                <span class="text-slate-400 font-mono">({{ $it->product->code }})</span>
                                                <span class="text-slate-500">&bull; Masuk Rak: {{ $it->product->primary_rack_code }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3 px-3 text-center font-extrabold text-slate-800">
                                    {{ $p->items->sum('qty') }} Unit
                                </td>
                                <td class="py-3 px-3 text-emerald-700 font-semibold">{{ $p->validated_by_warehouse }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 italic">Tidak ada penerimaan barang pemasok pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Modal Void Transaksi (Hanya Admin) -->
<div id="voidModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-800 text-base">Batalkan (Void) Transaksi Penjualan</h3>
            <button onclick="closeVoidModal()" class="text-slate-400 hover:text-slate-700 font-bold text-lg">&times;</button>
        </div>

        <form id="voidForm" method="POST" action="" class="space-y-4">
            @csrf
            <p class="text-xs text-slate-600">
                Apakah Anda yakin ingin membatalkan transaksi <strong id="voidInvoiceNo" class="font-mono text-blue-700"></strong>? 
                Stok barang akan otomatis dikembalikan ke gudang.
            </p>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan Pembatalan (Wajib Diisi) *</label>
                <textarea name="void_reason" required rows="3" placeholder="Contoh: Pelanggan salah beli ukuran part, pembatalan sepihak, salah input kasir..." 
                          class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3"></textarea>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="closeVoidModal()" class="px-4 py-2 border border-slate-200 text-slate-600 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow">
                    Konfirmasi Void
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function voidSaleModal(saleId, invoiceNo) {
        document.getElementById('voidInvoiceNo').innerText = invoiceNo;
        document.getElementById('voidForm').action = `/sales/${saleId}/void`;
        document.getElementById('voidModal').classList.remove('hidden');
    }
    function closeVoidModal() {
        document.getElementById('voidModal').classList.add('hidden');
    }
</script>
@endsection
