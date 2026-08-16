<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\Column;

/**
 * Only columns marked toggleable() are ever hidden — visibleColumns() lets
 * every other trait (HasSearch, HasSorting, HasSelection) keep operating
 * against the full columns() list, since hiding a column from *display* has
 * no bearing on whether it should still be searchable/sortable.
 */
trait HasColumnVisibility
{
    /**
     * @var array<int, string>
     */
    public array $hiddenColumns = [];

    /**
     * @return array<int, Column>
     */
    abstract public function columns(): array;

    /**
     * Optional session key prefix to persist the visibility choice across
     * requests. Returning null (the default) keeps it request-local.
     */
    protected function persistColumnVisibility(): ?string
    {
        return null;
    }

    /**
     * Livewire calls mount{TraitName}() on every trait automatically (see
     * SupportLifecycleHooks), independently of whatever mount() a consumer
     * subclass defines — the same mechanism HasUrlBinding's queryString()
     * sidesteps a different way, this is the mount-time equivalent.
     */
    public function mountHasColumnVisibility(): void
    {
        $sessionKey = $this->columnVisibilitySessionKey();

        if ($sessionKey !== null && session()->has($sessionKey)) {
            $this->hiddenColumns = session($sessionKey);

            return;
        }

        $this->hiddenColumns = $this->defaultHiddenColumns();
    }

    public function toggleColumnVisibility(string $field): void
    {
        $this->hiddenColumns = in_array($field, $this->hiddenColumns, true)
            ? array_values(array_diff($this->hiddenColumns, [$field]))
            : [...$this->hiddenColumns, $field];

        if ($sessionKey = $this->columnVisibilitySessionKey()) {
            session([$sessionKey => $this->hiddenColumns]);
        }
    }

    public function isColumnHidden(string $field): bool
    {
        return in_array($field, $this->hiddenColumns, true);
    }

    /**
     * @return array<int, Column>
     */
    public function visibleColumns(): array
    {
        return array_values(array_filter(
            $this->columns(),
            fn (Column $column) => ! ($column->isToggleable() && $this->isColumnHidden($column->getField()))
        ));
    }

    /**
     * @return array<int, Column>
     */
    public function toggleableColumns(): array
    {
        return array_values(array_filter($this->columns(), fn (Column $column) => $column->isToggleable()));
    }

    /**
     * @return array<int, string>
     */
    private function defaultHiddenColumns(): array
    {
        return array_values(array_map(
            fn (Column $column) => $column->getField(),
            array_filter(
                $this->columns(),
                fn (Column $column) => $column->isToggleable() && ! $column->isVisibleByDefault()
            )
        ));
    }

    private function columnVisibilitySessionKey(): ?string
    {
        $key = $this->persistColumnVisibility();

        return $key === null ? null : "livewire-datatable.column-visibility.{$key}";
    }
}
