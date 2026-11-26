<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Rack;
use App\Models\StockAdjustment;
use App\Services\StockService;
use Illuminate\Http\Request;
use Exception;

class StockController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Halaman Cek Stok
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $rackId = $request->input('rack_id');

        $query = Product::with(['category', 'unit', 'racks', 'primaryPhoto']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($rackId) {
            $query->whereHas('racks', function ($q) use ($rackId) {
                $q->where('racks.id', $rackId);
            });
        }

        if ($status === 'aman') {
            $query->whereColumn('stock', '>', 'min_stock');
        } elseif ($status === 'menipis') {
            $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
        } elseif ($status === 'habis') {
            $query->where('stock', '<=', 0);
        }

        $products = $query->orderBy('stock', 'asc')->paginate(20)->withQueryString();
        $categories = Category::all();
        $racks = Rack::where('is_active', true)->orderBy('code')->get();

        return view('stock.index', compact('products', 'categories', 'racks', 'search', 'categoryId', 'status', 'rackId'));
    }

    /**
     * Halaman Form Koreksi Stok & Riwayat
     */
    public function adjustment(Request $request)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $adjustments = StockAdjustment::with(['product.unit', 'creator'])
            ->latest()
            ->paginate(15);

        return view('stock.adjustment', compact('products', 'adjustments'));
    }

    /**
     * Proses Koreksi Stok
     */
    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_after' => 'required|integer|min:0',
            'reason' => 'required|string|min:3|max:500',
        ], [
            'reason.required' => 'Alasan koreksi stok wajib diisi.',
            'stock_after.min' => 'Stok akhir fisik tidak boleh negatif.',
        ]);

        try {
            $adjustment = $this->stockService->adjustStock(
                (int) $validated['product_id'],
                (int) $validated['stock_after'],
                $validated['reason'],
                auth()->id()
            );

            return redirect()->route('stock.adjustment')
                ->with('success', "Stok barang berhasil dikoreksi. Selisih: {$adjustment->difference}");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
