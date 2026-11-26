<?php

namespace App\Http\Controllers;

use App\Models\Rack;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $zone = $request->input('zone');

        $query = Rack::withCount('products');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($zone) {
            $query->where('zone', $zone);
        }

        $racks = $query->orderBy('code')->paginate(12)->withQueryString();
        $zones = Rack::select('zone')->whereNotNull('zone')->distinct()->pluck('zone');

        return view('racks.index', compact('racks', 'zones', 'search', 'zone'));
    }

    public function create()
    {
        return view('racks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:racks,code',
            'name' => 'required|string|max:100',
            'zone' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $rack = Rack::create($validated);
        ActivityLog::record('create', 'rack', "Menambahkan rak gudang {$rack->code} ({$rack->name})");

        return redirect()->route('racks.index')->with('success', "Rak {$rack->code} berhasil ditambahkan.");
    }

    public function show(Rack $rack)
    {
        $rack->load(['products' => function ($q) {
            $q->with(['category', 'unit', 'photos']);
        }]);

        return view('racks.show', compact('rack'));
    }

    public function edit(Rack $rack)
    {
        return view('racks.edit', compact('rack'));
    }

    public function update(Request $request, Rack $rack)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:racks,code,' . $rack->id,
            'name' => 'required|string|max:100',
            'zone' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $rack->update($validated);
        ActivityLog::record('update', 'rack', "Memperbarui rak gudang {$rack->code}");

        return redirect()->route('racks.index')->with('success', "Rak {$rack->code} berhasil diperbarui.");
    }

    public function destroy(Rack $rack)
    {
        $code = $rack->code;
        $rack->delete();
        ActivityLog::record('delete', 'rack', "Menghapus rak gudang {$code}");

        return redirect()->route('racks.index')->with('success', "Rak {$code} berhasil dihapus.");
    }
}
