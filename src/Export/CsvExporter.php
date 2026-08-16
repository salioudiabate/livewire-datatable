<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Export;

use RuntimeException;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams the export in chunks via DataSource::paginate() rather than
 * fetching everything with a single get() — the difference between working
 * and exhausting memory once a table has real production-sized data.
 */
final class CsvExporter implements Exporter
{
    public function __construct(private readonly int $chunkSize = 1000) {}

    public function export(DataSource $dataSource, array $columns, string $filename): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($dataSource, $columns) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                throw new RuntimeException('Unable to open the output stream for CSV export.');
            }

            // $escape is passed explicitly (empty string, the RFC4180-style
            // "no legacy backslash-escaping" mode) to opt into PHP 8.4's new
            // default now rather than emit its deprecation notice for every
            // row, and to get the same quoting behavior on PHP 8.3 too.
            fputcsv($handle, array_map(fn (Column $column) => $column->getLabel(), $columns), escape: '');

            $page = 1;

            while (true) {
                $result = $dataSource->paginate($this->chunkSize, $page);

                foreach ($result->items as $row) {
                    fputcsv($handle, array_map(
                        fn (Column $column) => (string) ($column->exportValue(data_get($row, $column->getField()), $row) ?? ''),
                        $columns
                    ), escape: '');
                }

                if ($result->items->isEmpty() || $page >= $result->lastPage()) {
                    break;
                }

                $page++;
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }
}
