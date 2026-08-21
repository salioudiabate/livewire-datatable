<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\RowAction;

trait HasRowActions
{
    abstract public function rows(): mixed;

    abstract public function resolveRowKey(mixed $row): string;

    /**
     * @return array<int, RowAction>
     */
    public function rowActions(): array
    {
        return [];
    }

    /**
     * @return array<int, RowAction>
     */
    public function visibleRowActions(mixed $row): array
    {
        return array_values(array_filter(
            $this->rowActions(),
            fn (RowAction $action) => $action->isVisible($row)
        ));
    }

    /**
     * The Blade view dispatches every action()-triggered row action through
     * this single method (wire:click="runRowAction('methodName', $key)")
     * rather than calling the target method name directly — same reasoning,
     * and the same defensive pattern, as HasToolbarActions::runToolbarAction()
     * and HasBulkActions::runBulkAction(): Livewire actions are callable by
     * name regardless of what the UI renders, so both "is this a real,
     * declared row action" and "is it visible() for *this* row" have to be
     * re-checked here — visible() alone only ever filtered rendering, not
     * who could actually trigger the underlying method.
     *
     * The row is re-resolved from the current page (rows(), the same
     * paginated, already-scoped-by-builder() result the row was rendered
     * from) rather than trusted from the client, so visible() is evaluated
     * against real data every time.
     */
    public function runRowAction(string $method, mixed $key): void
    {
        $action = null;

        foreach ($this->rowActions() as $candidate) {
            if ($candidate->getMethod() === $method) {
                $action = $candidate;

                break;
            }
        }

        if ($action === null) {
            abort(403);
        }

        $row = $this->findRowOnCurrentPage($key);

        if ($row === null || ! $action->isVisible($row)) {
            abort(403);
        }

        $this->{$method}($key);
    }

    private function findRowOnCurrentPage(mixed $key): mixed
    {
        $keyString = (string) $key;

        foreach ($this->rows()->items() as $row) {
            if ($this->resolveRowKey($row) === $keyString) {
                return $row;
            }
        }

        return null;
    }
}
