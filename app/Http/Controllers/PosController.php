<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Exception;

class PosController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    /**
     * Halaman Utama POS Kasir (2 Kolom Cepat)
     */
    public function index()
    {
        $categories = Category::all();
        $recentSales = Sale::where('status', 'completed')
            ->whereDate('sale_date', today())
            ->latest('sale_date')
            ->limit(10)
            ->get();

        return view('pos.index', compact('categories', 'recentSales'));
    }

    /**
     * Proses Checkout POS
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:tunai,transfer,qris',
            'idempotency_key' => 'required|string|max:64',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ], [
            'idempotency_key.required' => 'Token transaksi tidak valid.',
            'items.required' => 'Keranjang belanja masih kosong.',
            'paid_amount.required' => 'Jumlah uang yang dibayarkan wajib diisi.',
        ]);

        try {
            $sale = $this->saleService->createSale($validated, auth()->id());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil diproses.',
                    'sale_id' => $sale->id,
                    'invoice_no' => $sale->invoice_no,
                    'receipt_url' => route('pos.receipt', $sale),
                ]);
            }

            return redirect()->route('pos.success', $sale);
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman Sukses Setelah Bayar
     */
    public function success(Sale $sale)
    {
        $sale->load(['items.product', 'cashier']);
        return view('pos.success', compact('sale'));
    }

    /**
     * Cetak Struk Format Thermal 80mm (@media print)
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['items', 'cashier']);
        $settings = Setting::pluck('value', 'key');

        return view('pos.receipt', compact('sale', 'settings'));
    }

    /**
     * Cetak Nota Format PDF
     */
    public function receiptPdf(Sale $sale)
    {
        $sale->load(['items', 'cashier']);
        $settings = Setting::pluck('value', 'key');

        $pdf = Pdf::loadView('pos.receipt-pdf', compact('sale', 'settings'))
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // Ukuran roll thermal 80mm dalam points

        return $pdf->stream("Nota-{$sale->invoice_no}.pdf");
    }

    /**
     * Batalkan Transaksi (Void) oleh Admin
     */
    public function void(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'void_reason' => 'required|string|min:3|max:500',
        ], [
            'void_reason.required' => 'Alasan pembatalan (void) wajib diisi.',
        ]);

        try {
            $this->saleService->voidSale($sale->id, $validated['void_reason'], auth()->id());

            return back()->with('success', "Transaksi {$sale->invoice_no} berhasil dibatalkan (void) dan stok telah dikembalikan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
