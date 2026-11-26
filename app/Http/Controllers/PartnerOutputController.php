<?php

namespace App\Http\Controllers;

use App\Models\PartnerOutput;
use App\Models\PartnerCompany;
use App\Models\Product;
use App\Services\PartnerOutputService;
use Illuminate\Http\Request;
use Exception;

class PartnerOutputController extends Controller
{
    public function __construct(
        protected PartnerOutputService $partnerOutputService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $partnerId = $request->input('partner_company_id');

        $query = PartnerOutput::with(['partnerCompany', 'items.product', 'creator']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('output_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($partnerId) {
            $query->where('partner_company_id', $partnerId);
        }

        $outputs = $query->orderBy('output_date', 'desc')->paginate(15)->withQueryString();
        $partners = PartnerCompany::all();

        return view('partner-outputs.index', compact('outputs', 'partners', 'search', 'partnerId'));
    }

    public function create()
    {
        $partners = PartnerCompany::all();
        $products = Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();

        return view('partner-outputs.create', compact('partners', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_company_id' => 'required|exists:partner_companies,id',
            'output_date' => 'required|date',
            'delivery_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ], [
            'items.required' => 'Minimal harus ada 1 barang keluar.',
        ]);

        try {
            $output = $this->partnerOutputService->createPartnerOutput($validated, auth()->id());

            return redirect()->route('partner-outputs.show', $output)
                ->with('success', "Barang keluar rekanan {$output->output_no} berhasil dicatat dan stok fisik terpotong.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(PartnerOutput $partnerOutput)
    {
        $partnerOutput->load(['partnerCompany', 'items.product.unit', 'creator']);

        return view('partner-outputs.show', compact('partnerOutput'));
    }
}
