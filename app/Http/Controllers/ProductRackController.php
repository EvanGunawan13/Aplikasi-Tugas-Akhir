<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rack;
use App\Models\ProductRack;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ProductRackController extends Controller
{
    public function index(Request $request)
    {
        $rackId = $request->input('rack_id');
        $racks = Rack::withCount('products')->orderBy('code')->get();

        $selectedRack = null;
        $productsInRack = collect();

        if ($rackId) {
            $selectedRack = Rack::with(['products' => function ($q) {
                $q->with(['category', 'unit', 'photos']);
            }])->find($rackId);

            if ($selectedRack) {
                $productsInRack = $selectedRack->products;
            }
        }

        $allProducts = Product::orderBy('name')->get();

        return view('product-racks.index', compact('racks', 'selectedRack', 'productsInRack', 'allProducts', 'rackId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rack_id' => 'required|exists:racks,id',
            'is_primary' => 'boolean',
            'note' => 'nullable|string|max:255',
        ]);

        $isPrimary = $request->boolean('is_primary');

        // Jika diset sebagai rak utama, nonaktifkan primary pada relasi rak lainnya
        if ($isPrimary) {
            ProductRack::where('product_id', $validated['product_id'])->update(['is_primary' => false]);
        }

        ProductRack::updateOrCreate(
            [
                'product_id' => $validated['product_id'],
                'rack_id' => $validated['rack_id'],
            ],
            [
                'is_primary' => $isPrimary,
                'note' => $validated['note'] ?? null,
            ]
        );

        $product = Product::find($validated['product_id']);
        $rack = Rack::find($validated['rack_id']);
        ActivityLog::record('place_rack', 'warehouse', "Menempatkan barang {$product->code} ke rak {$rack->code}");

        return redirect()->route('product-racks.index', ['rack_id' => $rack->id])
            ->with('success', "Barang {$product->name} berhasil ditempatkan di rak {$rack->code}.");
    }

    public function destroy(Product $product, Rack $rack)
    {
        ProductRack::where('product_id', $product->id)->where('rack_id', $rack->id)->delete();
        ActivityLog::record('remove_rack', 'warehouse', "Menghapus barang {$product->code} dari rak {$rack->code}");

        return back()->with('success', "Barang berhasil dilepas dari rak {$rack->code}.");
    }
}
