<?php

namespace App\Services\Reporting;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExporter
{
    public function download(Collection $movements, string $format, string $filename, string $type): Response|StreamedResponse
    {
        $format = strtolower($format);

        return match ($format) {
            'pdf' => $this->asPdf($movements, $filename, $type),
            'csv' => $this->asCsv($movements, $filename, $type),
            default => abort(404, 'Formato no soportado'),
        };
    }

    private function asPdf(Collection $movements, string $filename, string $type): Response
    {
        $pdf = Pdf::loadView('reports.pdf', compact('movements', 'type'));

        return $pdf->download($filename.'.pdf');
    }

    private function asCsv(Collection $movements, string $filename, string $type): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
        ];

        $columns = $type === 'entry'
            ? ['Fecha', 'Producto', 'Categoría', 'Cantidad', 'Proveedor', 'Usuario', 'Referencia']
            : ['Fecha', 'Producto', 'Categoría', 'Cantidad', 'Usuario', 'Referencia'];

        return new StreamedResponse(function () use ($movements, $columns, $type) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($movements as $movement) {
                $row = [
                    $movement->createdAt->format('d/m/Y H:i'),
                    $movement->product?->name,
                    $movement->product?->category?->name,
                    $movement->quantity,
                ];

                if ($type === 'entry') {
                    $row[] = $movement->product?->supplier?->name;
                }

                $row[] = $movement->user?->name;
                $row[] = $movement->reference;

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
