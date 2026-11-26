<?php

namespace App\Http\Controllers;

use App\Models\PartnerCompany;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PartnerCompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = PartnerCompany::withCount('partnerOutputs');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $partners = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('partners.index', compact('partners', 'search'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $partner = PartnerCompany::create($validated);
        ActivityLog::record('create', 'partner', "Menambahkan perusahaan rekanan {$partner->name}");

        return redirect()->route('partners.index')->with('success', "Perusahaan rekanan {$partner->name} berhasil ditambahkan.");
    }

    public function edit(PartnerCompany $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, PartnerCompany $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $partner->update($validated);
        ActivityLog::record('update', 'partner', "Memperbarui perusahaan rekanan {$partner->name}");

        return redirect()->route('partners.index')->with('success', "Perusahaan rekanan {$partner->name} berhasil diperbarui.");
    }

    public function destroy(PartnerCompany $partner)
    {
        $name = $partner->name;
        $partner->delete();
        ActivityLog::record('delete', 'partner', "Menghapus rekanan {$name}");

        return redirect()->route('partners.index')->with('success', "Perusahaan rekanan {$name} berhasil dihapus.");
    }
}
