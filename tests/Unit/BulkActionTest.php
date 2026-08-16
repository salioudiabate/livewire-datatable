<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Gate;
use Salioudiabate\LivewireDatatable\BulkAction;

it('creates via the named constructor and exposes its basic properties', function () {
    $action = BulkAction::make('export', 'Export');

    expect($action->getMethod())->toBe('export')
        ->and($action->getLabel())->toBe('Export')
        ->and($action->needsConfirmation())->toBeFalse()
        ->and($action->getConfirmMessage())->toBeNull();
});

it('is authorized by default when no permission is set', function () {
    expect(BulkAction::make('export', 'Export')->isAuthorized())->toBeTrue();
});

it('is authorized only when the given gate ability is granted', function () {
    Gate::define('export-posts', fn ($user = null) => true);
    Gate::define('delete-posts', fn ($user = null) => false);

    expect(BulkAction::make('export', 'Export')->permission('export-posts')->isAuthorized())->toBeTrue()
        ->and(BulkAction::make('destroy', 'Delete')->permission('delete-posts')->isAuthorized())->toBeFalse();
});

it('carries a confirmation message once confirm() is set', function () {
    $action = BulkAction::make('destroy', 'Delete')->confirm('Are you sure?');

    expect($action->needsConfirmation())->toBeTrue()
        ->and($action->getConfirmMessage())->toBe('Are you sure?');
});

it('carries an optional css class and icon', function () {
    $action = BulkAction::make('destroy', 'Delete')->cssClass('btn-danger')->icon('trash');

    expect($action->getCssClass())->toBe('btn-danger')
        ->and($action->getIcon())->toBe('trash');
});

it('defaults to an empty css class when none is set', function () {
    expect(BulkAction::make('export', 'Export')->getCssClass())->toBe('');
});
