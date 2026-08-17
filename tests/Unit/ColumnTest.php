<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\Column;

it('creates via the named constructor', function () {
    $column = Column::make('Title', 'title');

    expect($column->getLabel())->toBe('Title')
        ->and($column->getField())->toBe('title');
});

it('renders the raw value when no formatter is set', function () {
    $column = Column::make('Title', 'title');

    expect($column->renderValue('Alpha', null))->toBe('Alpha');
});

it('renders through a custom formatter', function () {
    $column = Column::make('Title', 'title')->format(fn (mixed $value) => strtoupper((string) $value));

    expect($column->renderValue('alpha', null))->toBe('ALPHA');
});

it('is not searchable or sortable by default', function () {
    $column = Column::make('Title', 'title');

    expect($column->isSearchable())->toBeFalse()
        ->and($column->isSortable())->toBeFalse();
});

it('marks a column searchable with an optional custom closure', function () {
    $closure = fn () => null;
    $column = Column::make('Title', 'title')->searchable($closure);

    expect($column->isSearchable())->toBeTrue()
        ->and($column->getSearchUsing())->toBe($closure);
});

it('marks a column sortable and defaults the sort field to the display field', function () {
    $column = Column::make('Title', 'title')->sortable();

    expect($column->isSortable())->toBeTrue()
        ->and($column->getSortField())->toBe('title');
});

it('allows a distinct sort field for a display-only column', function () {
    $column = Column::make('Author', 'author.name')->sortable('author_id');

    expect($column->getSortField())->toBe('author_id');
});

it('sortUsing implies sortable', function () {
    $column = Column::make('Title', 'title')->sortUsing(fn () => null);

    expect($column->isSortable())->toBeTrue();
});

it('falls back to the raw value on export even if format() is set, since format() may return markup', function () {
    $column = Column::make('Status', 'status')->format(fn () => '<span>Published</span>');

    expect($column->exportValue('published', null))->toBe('published');
});

it('uses exportUsing for the export value when set', function () {
    $column = Column::make('Status', 'status')->exportUsing(fn (mixed $value) => strtoupper((string) $value));

    expect($column->exportValue('published', null))->toBe('PUBLISHED');
});

it('exposes a custom view for the header cell independently of the body cell view', function () {
    $column = Column::make('Actions', 'id')
        ->thView('partials.actions-header')
        ->view('partials.actions-cell');

    expect($column->getThView())->toBe('partials.actions-header')
        ->and($column->getView())->toBe('partials.actions-cell');
});

it('merges an extra class onto the header cell', function () {
    $column = Column::make('Actions', 'id')->thClass('text-right');

    expect($column->getThClass())->toBe('text-right');
});

it('is not frozen by default', function () {
    $column = Column::make('Title', 'title');

    expect($column->isFrozen())->toBeFalse()
        ->and($column->getWidth())->toBeNull();
});

it('marks a column frozen with its pixel width', function () {
    $column = Column::make('Title', 'title')->frozen(150);

    expect($column->isFrozen())->toBeTrue()
        ->and($column->getWidth())->toBe(150);
});
