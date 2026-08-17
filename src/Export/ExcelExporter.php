<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Export;

use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires maatwebsite/excel, an optional dependency: `composer require
 * maatwebsite/excel`. Pass a filename ending in an Excel-recognized
 * extension (e.g. .xlsx) — Excel::download() infers the writer from it.
 *
 * The DataSource is still read in chunks via paginate() rather than a
 * single get(), same as CsvExporter — but unlike CSV, the XLSX format
 * itself isn't row-streamable: PhpSpreadsheet has to hold the workbook in
 * memory while writing it, so this bounds *read* memory, not the final
 * write. For very large exports, prefer CsvExporter.
 */
final class ExcelExporter implements Exporter
{
    public function __construct(private readonly int $chunkSize = 1000) {}

    public function export(DataSource $dataSource, array $columns, string $filename): Response
    {
        if (! class_exists(Excel::class)) {
            throw new RuntimeException(
                'ExcelExporter requires maatwebsite/excel. Install it with: composer require maatwebsite/excel'
            );
        }

        return Excel::download(
            new class($dataSource, $columns, $this->chunkSize) implements FromCollection, WithHeadings, WithMapping
            {
                /**
                 * @param  array<int, Column>  $columns
                 */
                public function __construct(
                    private readonly DataSource $dataSource,
                    private readonly array $columns,
                    private readonly int $chunkSize,
                ) {}

                /**
                 * @return LazyCollection<int, mixed>
                 */
                public function collection(): LazyCollection
                {
                    $dataSource = $this->dataSource;
                    $chunkSize = $this->chunkSize;

                    return LazyCollection::make(function () use ($dataSource, $chunkSize) {
                        $page = 1;

                        while (true) {
                            $result = $dataSource->paginate($chunkSize, $page);

                            foreach ($result->items as $row) {
                                yield $row;
                            }

                            if ($result->items->isEmpty() || $page >= $result->lastPage()) {
                                break;
                            }

                            $page++;
                        }
                    });
                }

                /**
                 * @return array<int, string>
                 */
                public function headings(): array
                {
                    return array_map(fn (Column $column): string => $column->getLabel(), $this->columns);
                }

                /**
                 * @return array<int, mixed>
                 */
                public function map(mixed $row): array
                {
                    return array_map(
                        fn (Column $column): mixed => $column->exportValue(data_get($row, $column->getField()), $row) ?? '',
                        $this->columns
                    );
                }
            },
            $filename
        );
    }
}
