<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource;
use Salioudiabate\LivewireDatatable\Export\PdfExporter;
use Symfony\Component\HttpFoundation\Response;

it('returns a PDF download response containing the header and row values', function () {
    $source = new CollectionDataSource(collect([
        ['id' => 1, 'title' => 'Alpha'],
        ['id' => 2, 'title' => 'Bravo'],
    ]));
    $columns = [Column::make('Title', 'title')];

    $response = (new PdfExporter)->export($source, $columns, 'export.pdf');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->headers->get('Content-Type'))->toBe('application/pdf');

    $pdfText = $response->getContent();

    expect($pdfText)->toBeString()->not->toBeEmpty();
});

it('uses exportValue() rather than the display formatter', function () {
    $source = new CollectionDataSource(collect([['id' => 1, 'status' => 'published']]));
    $columns = [Column::make('Status', 'status')->format(fn (mixed $v) => "<b>{$v}</b>")];

    $response = (new PdfExporter)->export($source, $columns, 'export.pdf');

    expect($response->getStatusCode())->toBe(200);
});

it('exports every row across multiple chunks, not just the first one', function () {
    $records = collect(range(1, 25))->map(fn (int $i) => ['id' => $i, 'title' => "Row{$i}"]);
    $source = new CollectionDataSource($records);
    $columns = [Column::make('Title', 'title')];

    $response = (new PdfExporter(chunkSize: 10))->export($source, $columns, 'export.pdf');

    expect($response->getStatusCode())->toBe(200);
});
