<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemSearchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $query = Product::with(['category', 'unit', 'racks', 'photos', 'primaryPhoto']);

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

        $products = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('items.search', compact('products', 'categories', 'search', 'categoryId'));
    }

    /**
     * Endpoint JSON pencarian cepat untuk POS dan modal katalog
     */
    public function searchJson(Request $request)
    {
        $q = $request->input('q');

        $query = Product::with(['unit', 'racks', 'primaryPhoto', 'photos'])
            ->where('is_active', true);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('part_number', 'like', "%{$q}%");
            });
        }

        $products = $query->limit(20)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'code' => $p->code,
                'part_number' => $p->part_number ?? '-',
                'name' => $p->name,
                'price' => (float) $p->sell_price,
                'stock' => $p->stock,
                'unit' => $p->unit->symbol ?? $p->unit->name ?? 'Pcs',
                'rack_code' => $p->primary_rack_code,
                'photo_url' => $p->primary_photo_url,
                'photos' => $p->photos->map(fn($photo) => asset('storage/' . $photo->path)),
                'description' => $p->description,
            ];
        });

        return response()->json($products);
    }
}
