<?php

namespace App\Services\Reporting;

class DashboardService
{
    public function __construct(private ReportService $reportService) {}

    public function getData(): array
    {
        return $this->reportService->getDashboardData();
    }
}
