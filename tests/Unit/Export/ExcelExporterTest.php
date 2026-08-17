<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\IOFactory;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource;
use Salioudiabate\LivewireDatatable\Export\ExcelExporter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

function readExportedSheetRows(BinaryFileResponse $response): array
{
    $spreadsheet = IOFactory::load($response->getFile()->getPathname());

    return $spreadsheet->getActiveSheet()->toArray();
}

it('writes a header row using column labels, followed by one row per record', function () {
    $source = new CollectionDataSource(collect([
        ['id' => 1, 'title' => 'Alpha'],
        ['id' => 2, 'title' => 'Bravo'],
    ]));
    $columns = [Column::make('Title', 'title')];

    $response = (new ExcelExporter)->export($source, $columns, 'export.xlsx');

    expect($response)->toBeInstanceOf(BinaryFileResponse::class);

    $rows = readExportedSheetRows($response);

    expect($rows)->toBe([
        ['Title'],
        ['Alpha'],
        ['Bravo'],
    ]);
});

it('uses exportValue() rather than the display formatter', function () {
    $source = new CollectionDataSource(collect([['id' => 1, 'status' => 'published']]));
    $columns = [Column::make('Status', 'status')->format(fn (mixed $v) => "<b>{$v}</b>")];

    $response = (new ExcelExporter)->export($source, $columns, 'export.xlsx');
    $rows = readExportedSheetRows($response);

    expect($rows)->toBe([
        ['Status'],
        ['published'],
    ]);
});

it('respects exportUsing() when set', function () {
    $source = new CollectionDataSource(collect([['id' => 1, 'status' => 'published']]));
    $columns = [Column::make('Status', 'status')->exportUsing(fn (mixed $v) => strtoupper((string) $v))];

    $response = (new ExcelExporter)->export($source, $columns, 'export.xlsx');
    $rows = readExportedSheetRows($response);

    expect($rows[1])->toBe(['PUBLISHED']);
});

it('exports every row across multiple chunks, not just the first one', function () {
    $records = collect(range(1, 25))->map(fn (int $i) => ['id' => $i, 'title' => "Row{$i}"]);
    $source = new CollectionDataSource($records);
    $columns = [Column::make('Title', 'title')];

    $response = (new ExcelExporter(chunkSize: 10))->export($source, $columns, 'export.xlsx');
    $rows = readExportedSheetRows($response);

    expect($rows)->toHaveCount(26)
        ->and($rows[1])->toBe(['Row1'])
        ->and($rows[25])->toBe(['Row25']);
});
