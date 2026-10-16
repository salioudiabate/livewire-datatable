<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Support\HtmlString;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource;
use Salioudiabate\LivewireDatatable\Export\CsvExporter;
use Salioudiabate\LivewireDatatable\Export\ExportValue;

enum ExportValueGender: string
{
    case Male = 'M';
    case Female = 'F';

    public function label(): string
    {
        return $this === self::Male ? 'Masculin' : 'Féminin';
    }
}

enum ExportValueStatus: string
{
    case Draft = 'draft';
}

enum ExportValuePure
{
    case Open;
}

it('keeps strings, numbers and null as they are', function () {
    expect(ExportValue::normalize('a'))->toBe('a')
        ->and(ExportValue::normalize(12))->toBe(12)
        ->and(ExportValue::normalize(1.5))->toBe(1.5)
        ->and(ExportValue::normalize(null))->toBeNull();
});

it('exports an enum by its label, else its value or name', function () {
    expect(ExportValue::normalize(ExportValueGender::Female))->toBe('Féminin')
        ->and(ExportValue::normalize(ExportValueStatus::Draft))->toBe('draft')
        ->and(ExportValue::normalize(ExportValuePure::Open))->toBe('Open');
});

it('exports dates, booleans, markup and lists as text', function () {
    expect(ExportValue::normalize(CarbonImmutable::parse('2026-10-15')))->toBe('2026-10-15')
        ->and(ExportValue::normalize(CarbonImmutable::parse('2026-10-15 08:12:03')))->toBe('2026-10-15 08:12:03')
        ->and(ExportValue::normalize(true))->toBe('1')
        ->and(ExportValue::normalize(false))->toBe('0')
        ->and(ExportValue::normalize(new HtmlString('<b>Payé</b> &amp; reçu')))->toBe('Payé & reçu')
        ->and(ExportValue::normalize([ExportValueGender::Male, 'x', null]))->toBe('Masculin, x')
        ->and(ExportValue::normalize(collect([1, 2])))->toBe('1, 2')
        ->and(ExportValue::normalize(new stdClass))->toBe('');
});

it('exports a CSV with an enum column instead of crashing', function () {
    $source = new CollectionDataSource(collect([
        ['id' => 1, 'gender' => ExportValueGender::Male, 'born' => CarbonImmutable::parse('2018-05-02')],
        ['id' => 2, 'gender' => ExportValueGender::Female, 'born' => null],
    ]));
    $columns = [Column::make('Sexe', 'gender'), Column::make('Né le', 'born')];

    $csv = captureStreamedResponse((new CsvExporter)->export($source, $columns, 'export.csv'));

    expect(array_values(array_filter(explode("\n", $csv))))->toBe(['Sexe,"Né le"', 'Masculin,2018-05-02', 'Féminin,']);
});

it('normalizes what exportUsing() returns too', function () {
    $column = Column::make('Sexe', 'gender')->exportUsing(fn (mixed $value) => $value);

    expect($column->exportValue(ExportValueGender::Female, null))->toBe('Féminin');
});
