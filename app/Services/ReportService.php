<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\PartnerOutput;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Ambil data rekapitulasi berdasarkan filter tanggal dan jenis transaksi.
     */
    public function getTransactionSummary(string $startDate, string $endDate, string $type = 'all'): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $sales = collect();
        $partnerOutputs = collect();
        $purchases = collect();

        // 1. Penjualan Eceran (B2C)
        if ($type === 'all' || $type === 'sale') {
            $sales = Sale::with(['items.product', 'cashier'])
                ->whereBetween('sale_date', [$start, $end])
                ->where('status', 'completed')
                ->orderBy('sale_date', 'desc')
                ->get();
        }

        // 2. Barang Keluar Rekanan (B2B)
        if ($type === 'all' || $type === 'partner_out') {
            $partnerOutputs = PartnerOutput::with(['items.product', 'partnerCompany', 'creator'])
                ->whereBetween('output_date', [$start->toDateString(), $end->toDateString()])
                ->orderBy('output_date', 'desc')
                ->get();
        }

        // 3. Penerimaan dari Pemasok
        if ($type === 'all' || $type === 'purchase') {
            $purchases = Purchase::with(['items.product', 'supplier', 'creator'])
                ->whereBetween('purchase_date', [$start->toDateString(), $end->toDateString()])
                ->orderBy('purchase_date', 'desc')
                ->get();
        }

        // Ringkasan Statistik
        $totalSalesAmount = $sales->sum('grand_total');
        $totalSalesCount = $sales->count();
        $totalB2BQty = $partnerOutputs->flatMap->items->sum('qty');
        $totalPurchaseQty = $purchases->flatMap->items->sum('qty');

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'type' => $type,
            'sales' => $sales,
            'partner_outputs' => $partnerOutputs,
            'purchases' => $purchases,
            'summary' => [
                'total_sales_amount' => $totalSalesAmount,
                'total_sales_count' => $totalSalesCount,
                'total_b2b_qty' => $totalB2BQty,
                'total_purchase_qty' => $totalPurchaseQty,
            ],
        ];
    }
}
