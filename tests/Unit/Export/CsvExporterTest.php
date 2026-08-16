<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource;
use Salioudiabate\LivewireDatatable\Export\CsvExporter;

it('writes a header row using column labels', function () {
    $source = new CollectionDataSource(collect([['id' => 1, 'title' => 'Alpha']]));
    $columns = [Column::make('Title', 'title')];

    $csv = captureStreamedResponse((new CsvExporter)->export($source, $columns, 'export.csv'));

    expect(explode("\n", $csv)[0])->toBe('Title');
});

it('writes one row per record, using exportValue() rather than the display formatter', function () {
    $source = new CollectionDataSource(collect([
        ['id' => 1, 'status' => 'published'],
        ['id' => 2, 'status' => 'draft'],
    ]));
    $columns = [Column::make('Status', 'status')->format(fn (mixed $v) => "<b>{$v}</b>")];

    $csv = captureStreamedResponse((new CsvExporter)->export($source, $columns, 'export.csv'));
    $lines = array_values(array_filter(explode("\n", $csv)));

    expect($lines)->toBe(['Status', 'published', 'draft']);
});

it('respects exportUsing() when set', function () {
    $source = new CollectionDataSource(collect([['id' => 1, 'status' => 'published']]));
    $columns = [Column::make('Status', 'status')->exportUsing(fn (mixed $v) => strtoupper((string) $v))];

    $csv = captureStreamedResponse((new CsvExporter)->export($source, $columns, 'export.csv'));

    expect(trim(explode("\n", $csv)[1]))->toBe('PUBLISHED');
});

it('exports every row across multiple chunks, not just the first one', function () {
    $rows = collect(range(1, 25))->map(fn (int $i) => ['id' => $i, 'title' => "Row{$i}"]);
    $source = new CollectionDataSource($rows);
    $columns = [Column::make('Title', 'title')];

    $csv = captureStreamedResponse((new CsvExporter(chunkSize: 10))->export($source, $columns, 'export.csv'));
    $lines = array_values(array_filter(explode("\n", $csv)));

    expect($lines)->toHaveCount(26)
        ->and($lines[1])->toBe('Row1')
        ->and($lines[25])->toBe('Row25');
});

it('exports nothing but the header row when there are no matching records', function () {
    $source = new CollectionDataSource(collect());
    $columns = [Column::make('Title', 'title')];

    $csv = captureStreamedResponse((new CsvExporter)->export($source, $columns, 'export.csv'));

    expect(trim($csv))->toBe('Title');
});

it('sets a csv content type and an attachment content-disposition header', function () {
    $source = new CollectionDataSource(collect());
    $response = (new CsvExporter)->export($source, [], 'my-export.csv');

    expect($response->headers->get('Content-Type'))->toBe('text/csv')
        ->and($response->headers->get('Content-Disposition'))->toContain('my-export.csv');
});
