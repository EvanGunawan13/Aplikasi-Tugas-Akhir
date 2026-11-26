<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Exception;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $supplierId = $request->input('supplier_id');

        $query = Purchase::with(['supplier', 'items.product', 'creator']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_no', 'like', "%{$search}%")
                  ->orWhere('supplier_doc_no', 'like', "%{$search}%");
            });
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(15)->withQueryString();
        $suppliers = Supplier::all();

        return view('purchases.index', compact('purchases', 'suppliers', 'search', 'supplierId'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'supplier_doc_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'validated_by_warehouse' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.buy_price' => 'nullable|numeric|min:0',
        ], [
            'validated_by_warehouse.required' => 'Fisik barang wajib divalidasi oleh petugas gudang.',
            'items.required' => 'Minimal harus ada 1 barang pembelian.',
        ]);

        try {
            $purchase = $this->purchaseService->createPurchase($validated, auth()->id());

            return redirect()->route('purchases.show', $purchase)
                ->with('success', "Pembelian {$purchase->purchase_no} berhasil dicatat dan stok barang telah bertambah.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product.unit', 'creator']);

        return view('purchases.show', compact('purchase'));
    }
}
