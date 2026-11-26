<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Rack;
use App\Models\ProductRack;
use App\Models\ProductPhoto;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $rackId = $request->input('rack_id');

        $query = Product::with(['category', 'unit', 'racks', 'primaryPhoto', 'photos']);

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

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Category::all();
        $racks = Rack::where('is_active', true)->orderBy('code')->get();

        return view('products.index', compact('products', 'categories', 'racks', 'search', 'categoryId', 'status', 'rackId'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        $racks = Rack::where('is_active', true)->orderBy('code')->get();

        return view('products.create', compact('categories', 'units', 'racks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code',
            'part_number' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'rack_id' => 'nullable|exists:racks,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product = Product::create([
            'code' => $validated['code'],
            'part_number' => $validated['part_number'],
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'unit_id' => $validated['unit_id'],
            'buy_price' => $validated['buy_price'],
            'sell_price' => $validated['sell_price'],
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'],
            'description' => $validated['description'],
            'is_active' => true,
        ]);

        // Hubungkan ke rak bila dipilih
        if (!empty($validated['rack_id'])) {
            ProductRack::create([
                'product_id' => $product->id,
                'rack_id' => $validated['rack_id'],
                'is_primary' => true,
                'note' => 'Penempatan awal',
            ]);
        }

        // Upload foto
        if ($request->hasFile('photos')) {
            $isFirst = true;
            foreach ($request->file('photos') as $file) {
                $path = $file->store('products', 'public');
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'is_primary' => $isFirst,
                    'uploaded_by' => auth()->id(),
                ]);
                $isFirst = false;
            }
        }

        ActivityLog::record('create', 'product', "Menambahkan suku cadang {$product->code} - {$product->name}");

        return redirect()->route('products.index')->with('success', "Barang {$product->name} berhasil ditambahkan.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'racks', 'photos.uploader', 'stockMovements' => function ($q) {
            $q->latest('created_at')->limit(20);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = Unit::all();
        $racks = Rack::where('is_active', true)->orderBy('code')->get();
        $product->load(['racks', 'photos']);

        return view('products.edit', compact('product', 'categories', 'units', 'racks'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'part_number' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product->update($validated);

        // Upload foto tambahan jika ada
        if ($request->hasFile('photos')) {
            $hasPrimary = $product->photos()->where('is_primary', true)->exists();
            foreach ($request->file('photos') as $file) {
                $path = $file->store('products', 'public');
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'is_primary' => !$hasPrimary,
                    'uploaded_by' => auth()->id(),
                ]);
                $hasPrimary = true;
            }
        }

        ActivityLog::record('update', 'product', "Memperbarui suku cadang {$product->code}");

        return redirect()->route('products.index')->with('success', "Barang {$product->name} berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        ActivityLog::record('delete', 'product', "Menghapus suku cadang {$name}");

        return redirect()->route('products.index')->with('success', "Barang {$name} berhasil dihapus.");
    }

    public function setPrimaryPhoto(Product $product, ProductPhoto $photo)
    {
        ProductPhoto::where('product_id', $product->id)->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return back()->with('success', 'Foto utama berhasil diatur.');
    }

    public function deletePhoto(Product $product, ProductPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
