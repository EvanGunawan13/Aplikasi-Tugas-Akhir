<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota {{ $sale->invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #111;
            margin: 0;
            padding: 5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #666; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; font-size: 9px; }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="font-bold" style="font-size: 13px;">{{ $settings['store_name'] ?? 'CV MANDIRI DIESEL' }}</div>
        <div style="font-size: 9px;">{{ $settings['store_address'] ?? 'Jl Raden Muhammad No.261, 24 Ilir, Kec. Bukit Kecil, Kota Palembang, Sumatera Selatan' }}</div>
        <div style="font-size: 9px;">Telp: {{ $settings['store_phone'] ?? '+62 852-6897-9925' }}</div>
    </div>

    <div class="divider"></div>

    <table>
        <tr>
            <td>No: <strong>{{ $sale->invoice_no }}</strong></td>
            <td class="text-right">{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ $sale->cashier->name }}</td>
            <td class="text-right">Plg: {{ $sale->customer_name }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2" class="font-bold">{{ $item->product_name_snapshot }}</td>
            </tr>
            <tr>
                <td>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }} [Rak: {{ $item->product->primary_rack_code }}]</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($sale->delivery_fee > 0)
            <tr>
                <td>Ongkir Pengantaran:</td>
                <td class="text-right">{{ number_format($sale->delivery_fee, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="font-bold" style="font-size: 11px;">
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

    <div class="divider"></div>

    <div class="text-center" style="font-size: 8px; margin-top: 5px;">
        {{ $settings['receipt_footer'] ?? 'Terima kasih atas kunjungan Anda.' }}
    </div>
</body>
</html>
