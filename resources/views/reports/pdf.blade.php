<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Transaksi {{ $start_date }} sd {{ $end_date }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #222;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0 3px 0;
            text-transform: uppercase;
        }
        .report-period {
            font-size: 11px;
            text-align: center;
            color: #555;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            margin-bottom: 15px;
        }
        .summary-box td {
            font-size: 10px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
        }
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 9px;
            vertical-align: top;
        }
        table.data-table tfoot td {
            background-color: #f8fafc;
            font-weight: bold;
            border: 1px solid #cbd5e1;
        }
        .section-heading {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 10px;
            margin-bottom: 5px;
            padding-left: 2px;
            border-left: 3px solid #2563eb;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            width: 50%;
            font-size: 10px;
        }
        .signature-space {
            height: 50px;
        }
    </style>
</head>
<body>
    <!-- Kop Surat CV Mandiri Diesel -->
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    <div class="company-name">{{ $settings['store_name'] ?? 'CV MANDIRI DIESEL' }}</div>
                    <div style="font-size: 10px; color: #475569;">{{ $settings['store_tagline'] ?? 'Pusat Sparepart Alat Berat & Diesel Engine' }}</div>
                    <div style="font-size: 9px; color: #64748b;">{{ $settings['store_address'] ?? 'Jl Raden Muhammad No.261, 24 Ilir, Kec. Bukit Kecil, Kota Palembang, Sumatera Selatan' }} | Telp: {{ $settings['store_phone'] ?? '+62 852-6897-9925' }}</div>
                </td>
                <td style="width: 30%; text-align: right; font-size: 9px; color: #64748b;">
                    <div>Dicetak oleh: <strong>{{ $printed_by }}</strong></div>
                    <div>Waktu: {{ $print_date }} WIB</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Judul & Periode -->
    <div class="report-title">Laporan Rekapitulasi Arus Transaksi & Barang</div>
    <div class="report-period">
        Periode: <strong>{{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</strong>
    </div>

    <!-- Kotak Ringkasan Statistik -->
    <div class="summary-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 25%;">Total Penjualan B2C: <strong>Rp {{ number_format($summary['total_sales_amount'], 0, ',', '.') }}</strong></td>
                <td style="width: 25%;">Jumlah Faktur POS: <strong>{{ $summary['total_sales_count'] }} Transaksi</strong></td>
                <td style="width: 25%;">Barang Keluar Rekanan: <strong>{{ $summary['total_b2b_qty'] }} Pcs/Unit</strong></td>
                <td style="width: 25%;">Penerimaan Pemasok: <strong>{{ $summary['total_purchase_qty'] }} Pcs/Unit</strong></td>
            </tr>
        </table>
    </div>

    <!-- 1. Penjualan Eceran (B2C) -->
    @if ($type === 'all' || $type === 'sale')
        <div class="section-heading">1. Penjualan Eceran (B2C)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;" class="text-center">No.</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 14%;">No. Invoice</th>
                    <th style="width: 14%;">Pelanggan</th>
                    <th style="width: 34%;">Rincian Suku Cadang</th>
                    <th style="width: 8%;" class="text-right">Ongkir</th>
                    <th style="width: 16%;" class="text-right">Total Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $idx => $s)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $s->sale_date->format('d/m/Y H:i') }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $s->invoice_no }}</td>
                        <td>{{ $s->customer_name }}</td>
                        <td>
                            @foreach ($s->items as $it)
                                &bull; {{ $it->qty }}x {{ $it->product_name_snapshot }} (Rak: {{ $it->product->primary_rack_code }})<br>
                            @endforeach
                        </td>
                        <td class="text-right">{{ $s->delivery_fee > 0 ? number_format($s->delivery_fee, 0, ',', '.') : '-' }}</td>
                        <td class="text-right" style="font-weight: bold;">Rp {{ number_format($s->grand_total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="font-style: italic; color: #94a3b8;">Tidak ada transaksi penjualan eceran.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right">SUBTOTAL PENJUALAN ECERAN (B2C):</td>
                    <td class="text-right">Rp {{ number_format($sales->sum('grand_total'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- 2. Barang Keluar Rekanan (B2B) -->
    @if ($type === 'all' || $type === 'partner_out')
        <div class="section-heading">2. Barang Keluar Rekanan (B2B - Sinkronisasi Accurate)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;" class="text-center">No.</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 14%;">No. Pengeluaran</th>
                    <th style="width: 22%;">Perusahaan Rekanan</th>
                    <th style="width: 32%;">Daftar Suku Cadang Fisik</th>
                    <th style="width: 8%;" class="text-center">Qty Total</th>
                    <th style="width: 10%;">Catatan / DO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($partner_outputs as $idx => $out)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $out->output_date->format('d/m/Y') }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $out->output_no }}</td>
                        <td>{{ $out->partnerCompany->name }}</td>
                        <td>
                            @foreach ($out->items as $it)
                                &bull; {{ $it->qty }}x {{ $it->product->name }} (Rak: {{ $it->product->primary_rack_code }})<br>
                            @endforeach
                        </td>
                        <td class="text-center" style="font-weight: bold;">{{ $out->items->sum('qty') }} Unit</td>
                        <td>{{ $out->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="font-style: italic; color: #94a3b8;">Tidak ada barang keluar rekanan.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">TOTAL KUANTITAS FISIK KELUAR B2B:</td>
                    <td class="text-center">{{ $partner_outputs->flatMap->items->sum('qty') }} Unit</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- 3. Penerimaan Suku Cadang dari Pemasok -->
    @if ($type === 'all' || $type === 'purchase')
        <div class="section-heading">3. Penerimaan Suku Cadang dari Pemasok</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;" class="text-center">No.</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 14%;">No. Pembelian</th>
                    <th style="width: 20%;">Pemasok (Vendor)</th>
                    <th style="width: 12%;">No. SJ Vendor</th>
                    <th style="width: 26%;">Daftar Suku Cadang Diterima</th>
                    <th style="width: 6%;" class="text-center">Qty</th>
                    <th style="width: 8%;">Validasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $idx => $pur)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $pur->purchase_date->format('d/m/Y') }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $pur->purchase_no }}</td>
                        <td>{{ $pur->supplier->name }}</td>
                        <td>{{ $pur->supplier_doc_no ?? '-' }}</td>
                        <td>
                            @foreach ($pur->items as $it)
                                &bull; {{ $it->qty }}x {{ $it->product->name }} (Masuk Rak: {{ $it->product->primary_rack_code }})<br>
                            @endforeach
                        </td>
                        <td class="text-center" style="font-weight: bold;">{{ $pur->items->sum('qty') }} Unit</td>
                        <td style="font-size: 8px;">{{ $pur->validated_by_warehouse }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="font-style: italic; color: #94a3b8;">Tidak ada penerimaan barang pemasok.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right">TOTAL KUANTITAS FISIK MASUK:</td>
                    <td class="text-center">{{ $purchases->flatMap->items->sum('qty') }} Unit</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- Tanda Tangan Resmi Pemilik / Admin -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Pemilik CV Mandiri Diesel</strong>
                <div class="signature-space"></div>
                ( ___________________________ )
            </td>
            <td>
                Dibuat & Diverifikasi Oleh,<br>
                <strong>Admin / Petugas Operasional</strong>
                <div class="signature-space"></div>
                ( {{ $printed_by }} )
            </td>
        </tr>
    </table>
</body>
</html>
