<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use RuntimeException;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires barryvdh/laravel-dompdf, an optional dependency: `composer
 * require barryvdh/laravel-dompdf`.
 *
 * The DataSource is still read in chunks via paginate() rather than a
 * single get(), same as CsvExporter/ExcelExporter — but dompdf has no
 * streaming render API at all, so unlike CSV this bounds neither read nor
 * write memory: the full row set has to be held and the whole document
 * rendered before a single byte is sent. This exporter is meant for a
 * print-friendly report on a reasonably sized (typically already filtered)
 * result set — not bulk data export. Prefer CsvExporter for that.
 */
final class PdfExporter implements Exporter
{
    public function __construct(private readonly int $chunkSize = 1000) {}

    public function export(DataSource $dataSource, array $columns, string $filename): Response
    {
        if (! class_exists(Pdf::class)) {
            throw new RuntimeException(
                'PdfExporter requires barryvdh/laravel-dompdf. Install it with: composer require barryvdh/laravel-dompdf'
            );
        }

        $rows = [];
        $page = 1;

        while (true) {
            $result = $dataSource->paginate($this->chunkSize, $page);

            foreach ($result->items as $row) {
                $rows[] = array_map(
                    fn (Column $column) => (string) ($column->exportValue(data_get($row, $column->getField()), $row) ?? ''),
                    $columns
                );
            }

            if ($result->items->isEmpty() || $page >= $result->lastPage()) {
                break;
            }

            $page++;
        }

        return Pdf::loadView('livewire-datatable::exports.pdf', [
            'headings' => array_map(fn (Column $column) => $column->getLabel(), $columns),
            'rows' => $rows,
        ])->download($filename);
    }
}
