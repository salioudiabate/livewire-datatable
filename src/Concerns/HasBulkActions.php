<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\BulkAction;

trait HasBulkActions
{
    /**
     * @return array<int, BulkAction>
     */
    public function bulkActions(): array
    {
        return [];
    }

    /**
     * @return array<int, BulkAction>
     */
    public function authorizedBulkActions(): array
    {
        return array_values(array_filter(
            $this->bulkActions(),
            fn (BulkAction $action) => $action->isAuthorized()
        ));
    }

    /**
     * The Blade view dispatches every bulk action through this single
     * method (`wire:click="runBulkAction('methodName')"`) rather than
     * calling the target method name directly — Livewire actions are
     * callable by name regardless of what the UI renders, so authorization
     * has to be re-checked here, not just at button-render time.
     */
    public function runBulkAction(string $method): void
    {
        $action = null;

        foreach ($this->bulkActions() as $candidate) {
            if ($candidate->getMethod() === $method) {
                $action = $candidate;

                break;
            }
        }

        if ($action === null || ! $action->isAuthorized()) {
            abort(403);
        }

        $this->{$method}();
    }
}
