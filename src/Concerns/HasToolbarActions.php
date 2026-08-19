<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\ToolbarAction;
use Salioudiabate\LivewireDatatable\ToolbarActionGroup;

trait HasToolbarActions
{
    /**
     * @return array<int, ToolbarAction|ToolbarActionGroup>
     */
    public function toolbarActions(): array
    {
        return [];
    }

    /**
     * The toolbar view dispatches every action() trigger through this
     * single method (wire:click="runToolbarAction('methodName')") rather
     * than calling the target method name directly — Livewire actions are
     * callable by name regardless of what the UI renders, so authorization
     * has to be re-checked here, the same defensive pattern as
     * HasBulkActions::runBulkAction().
     */
    public function runToolbarAction(string $method): void
    {
        $action = null;

        foreach ($this->toolbarActions() as $item) {
            $candidates = $item instanceof ToolbarActionGroup ? $item->getActions() : [$item];

            foreach ($candidates as $candidate) {
                if ($candidate->getMethod() === $method) {
                    $action = $candidate;

                    break 2;
                }
            }
        }

        if ($action === null || ! $action->isAuthorized()) {
            abort(403);
        }

        $this->{$method}();
    }
}
