<?php

namespace App\Http\Controllers;

use App\Services\Reporting\ReportExporter;
use App\Services\Reporting\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private ReportExporter $reportExporter
    ) {}

    public function dashboard(): View
    {
        $data = $this->reportService->getDashboardData();

        return view('dashboard', $data);
    }

    public function entries(Request $request): View
    {
        $movements = $this->reportService->getMovements($request->all(), 'entry');
        $filters = $this->reportService->getFilterOptions();

        return view('reports.entries', array_merge(compact('movements'), $filters));
    }

    public function exits(Request $request): View
    {
        $movements = $this->reportService->getMovements($request->all(), 'exit');
        $filters = $this->reportService->getFilterOptions();

        return view('reports.exits', array_merge(compact('movements'), $filters));
    }

    public function exportEntries(Request $request, string $format)
    {
        $movements = $this->reportService->getMovements($request->all(), 'entry', false);
        $filename = $this->reportService->buildExportFilename('entry');

        return $this->reportExporter->download($movements, $format, $filename, 'entry');
    }

    public function exportExits(Request $request, string $format)
    {
        $movements = $this->reportService->getMovements($request->all(), 'exit', false);
        $filename = $this->reportService->buildExportFilename('exit');

        return $this->reportExporter->download($movements, $format, $filename, 'exit');
    }
}
