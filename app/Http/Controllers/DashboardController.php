<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Rack;
use App\Models\Purchase;
use App\Models\PartnerOutput;
use App\Models\ActivityLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Redirect kasir langsung ke halaman POS agar cepat dan efisien
        if ($user->role === 'kasir') {
            return redirect()->route('pos.index');
        }

        $today = Carbon::today();

        // Penjualan hari ini
        $todaySalesAmount = Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->sum('grand_total');

        $todaySalesCount = Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->count();

        // Barang stok menipis
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->with(['category', 'unit', 'racks'])
            ->orderBy('stock', 'asc')
            ->limit(8)
            ->get();

        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $totalProducts = Product::count();
        $totalRacks = Rack::where('is_active', true)->count();

        // Grafik Penjualan 7 Hari Terakhir
        $salesChartData = [];
        $salesChartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $salesChartLabels[] = $date->translatedFormat('d M');
            $salesChartData[] = (float) Sale::whereDate('sale_date', $date)
                ->where('status', 'completed')
                ->sum('grand_total');
        }

        // 5 Aktivitas Terakhir
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik Tambahan untuk Petugas Gudang / Admin
        $recentPurchases = Purchase::with('supplier')->latest()->limit(5)->get();
        $recentPartnerOutputs = PartnerOutput::with('partnerCompany')->latest()->limit(5)->get();

        return view('dashboard', compact(
            'todaySalesAmount',
            'todaySalesCount',
            'lowStockProducts',
            'lowStockCount',
            'totalProducts',
            'totalRacks',
            'salesChartLabels',
            'salesChartData',
            'recentActivities',
            'recentPurchases',
            'recentPartnerOutputs'
        ));
    }
}
