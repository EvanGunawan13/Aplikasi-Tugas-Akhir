<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Halaman Rekapitulasi Transaksi per Periode
     */
    public function index(Request $request)
    {
        $preset = $request->input('preset', 'today');
        $type = $request->input('type', 'all');

        $today = Carbon::today();
        switch ($preset) {
            case '7_days':
                $startDate = $today->copy()->subDays(6)->toDateString();
                $endDate = $today->toDateString();
                break;
            case 'this_month':
                $startDate = $today->copy()->startOfMonth()->toDateString();
                $endDate = $today->copy()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $startDate = $today->copy()->subMonth()->startOfMonth()->toDateString();
                $endDate = $today->copy()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'custom':
                $startDate = $request->input('start_date', $today->toDateString());
                $endDate = $request->input('end_date', $today->toDateString());
                break;
            case 'today':
            default:
                $preset = 'today';
                $startDate = $today->toDateString();
                $endDate = $today->toDateString();
                break;
        }

        $reportData = $this->reportService->getTransactionSummary($startDate, $endDate, $type);

        return view('reports.index', array_merge($reportData, [
            'preset' => $preset,
            'selected_type' => $type,
        ]));
    }

    /**
     * Unduh & Cetak Rekapitulasi PDF Resmi (A4 Landscape)
     */
    public function downloadPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $type = $request->input('type', 'all');

        $reportData = $this->reportService->getTransactionSummary($startDate, $endDate, $type);
        $settings = Setting::pluck('value', 'key');
        $printedBy = auth()->user()->name;
        $printDate = now()->translatedFormat('d F Y, H:i');

        $pdf = Pdf::loadView('reports.pdf', array_merge($reportData, [
            'settings' => $settings,
            'printed_by' => $printedBy,
            'print_date' => $printDate,
        ]))->setPaper('a4', 'landscape');

        $filename = "Rekap-Transaksi-{$startDate}-sd-{$endDate}.pdf";

        if ($request->input('action') === 'stream') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
