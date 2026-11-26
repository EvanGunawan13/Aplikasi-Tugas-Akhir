<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $sale->invoice_no }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 10px;
            width: 72mm;
            max-width: 80mm;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; margin-top: 5px; padding-top: 5px; }
        .border-bottom { border-bottom: 1px dashed #000; margin-bottom: 5px; padding-bottom: 5px; }
        .item-table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .item-table td { padding: 2px 0; vertical-align: top; }
        .btn-print {
            display: block;
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-family: sans-serif;
        }
        @media print {
            .btn-print { display: none; }
            body { padding: 5px 0; }
        }
    </style>
</head>
<body>
    <button type="button" class="btn-print" onclick="window.print()">CETAK STRUK (CTRL+P)</button>

    <!-- Header Toko -->
    <div class="text-center">
        <div class="font-bold" style="font-size: 15px;">{{ $settings['store_name'] ?? 'CV MANDIRI DIESEL' }}</div>
        <div style="font-size: 10px;">{{ $settings['store_tagline'] ?? 'Pusat Sparepart Alat Berat & Diesel' }}</div>
        <div style="font-size: 10px;">{{ $settings['store_address'] ?? 'Jl Raden Muhammad No.261, 24 Ilir, Kec. Bukit Kecil, Kota Palembang, Sumatera Selatan' }}</div>
        <div style="font-size: 10px;">Telp: {{ $settings['store_phone'] ?? '+62 852-6897-9925' }}</div>
    </div>

    <div class="border-top border-bottom" style="font-size: 11px;">
        <table style="width: 100%;">
            <tr>
                <td>No: {{ $sale->invoice_no }}</td>
                <td class="text-right">{{ $sale->sale_date->format('d/m/y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir: {{ $sale->cashier->name }}</td>
                <td class="text-right">Plg: {{ substr($sale->customer_name, 0, 10) }}</td>
            </tr>
        </table>
    </div>

    <!-- Rincian Barang -->
    <table class="item-table">
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="3" class="font-bold">{{ $item->product_name_snapshot }}</td>
            </tr>
            <tr style="font-size: 11px;">
                <td style="width: 45%;">{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</td>
                <td style="width: 15%; text-align: center;">[{{ $item->product->primary_rack_code }}]</td>
                <td class="text-right" style="width: 40%;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <!-- Total & Pembayaran -->
    <div class="border-top" style="font-size: 11px;">
        <table style="width: 100%;">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if ($sale->delivery_fee > 0)
                <tr>
                    <td>Ongkir:</td>
                    <td class="text-right">{{ number_format($sale->delivery_fee, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="font-bold" style="font-size: 13px;">
                <td>TOTAL:</td>
                <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar ({{ strtoupper($sale->payment_method) }}):</td>
                <td class="text-right">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian:</td>
                <td class="text-right font-bold">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer Nota -->
    <div class="border-top text-center" style="font-size: 9px; margin-top: 8px;">
        <p>{{ $settings['receipt_footer'] ?? 'Terima kasih atas kunjungan Anda. Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa nota resmi.' }}</p>
    </div>

    <script>
        window.onload = function() {
            // Otomatis muncul dialog print jika dibuka
            // window.print();
        };
    </script>
</body>
</html>
