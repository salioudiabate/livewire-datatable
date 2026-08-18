<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Closure;

/**
 * Fluent, immutable-style column definition. `format()`/`exportUsing()` are
 * kept deliberately distinct: a format() closure may return markup for
 * screen display, which would be meaningless (or actively wrong) written
 * into a CSV cell, so exports fall back to the raw value, never to format().
 */
final class Column
{
    private ?Closure $formatter = null;

    private ?string $view = null;

    private ?string $thView = null;

    private string $thClass = '';

    private string $tdClass = '';

    private bool $searchable = false;

    private ?Closure $searchUsing = null;

    private bool $sortable = false;

    private ?string $sortField = null;

    private ?Closure $sortUsing = null;

    private ?Closure $exportUsing = null;

    private bool $toggleable = false;

    private bool $visibleByDefault = true;

    private bool $frozen = false;

    private ?int $width = null;

    public function __construct(
        private readonly string $label,
        private readonly string $field,
    ) {}

    public static function make(string $label, string $field): static
    {
        return new self($label, $field);
    }

    /**
     * @param  Closure(mixed $value, mixed $row): mixed  $formatter
     */
    public function format(Closure $formatter): static
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function thView(string $view): static
    {
        $this->thView = $view;

        return $this;
    }

    public function thClass(string $class): static
    {
        $this->thClass = $class;

        return $this;
    }

    public function tdClass(string $class): static
    {
        $this->tdClass = $class;

        return $this;
    }

    /**
     * @param  (Closure(mixed $dataSource, string $term): mixed)|null  $using  Full escape hatch against the raw underlying query, for relation/multi-field search.
     */
    public function searchable(?Closure $using = null): static
    {
        $this->searchable = true;
        $this->searchUsing = $using;

        return $this;
    }

    public function sortable(?string $sortField = null): static
    {
        $this->sortable = true;
        $this->sortField = $sortField;

        return $this;
    }

    /**
     * @param  Closure(mixed $dataSource, 'asc'|'desc' $direction): mixed  $callback
     */
    public function sortUsing(Closure $callback): static
    {
        $this->sortable = true;
        $this->sortUsing = $callback;

        return $this;
    }

    /**
     * @param  Closure(mixed $value, mixed $row): mixed  $callback
     */
    public function exportUsing(Closure $callback): static
    {
        $this->exportUsing = $callback;

        return $this;
    }

    public function toggleable(bool $visibleByDefault = true): static
    {
        $this->toggleable = true;
        $this->visibleByDefault = $visibleByDefault;

        return $this;
    }

    /**
     * Pins this column in place while the rest of a wide table scrolls
     * horizontally. Requires an explicit pixel $width: there's no way to
     * measure a rendered column's actual width from PHP, and computing a
     * frozen column's sticky offset needs the widths of every frozen column
     * before it. Frozen columns must form a leading, contiguous run of
     * columns() — see Concerns\HasFrozenColumns.
     */
    public function frozen(int $width): static
    {
        $this->frozen = true;
        $this->width = $width;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function getThView(): ?string
    {
        return $this->thView;
    }

    public function getThClass(): string
    {
        return $this->thClass;
    }

    public function getTdClass(): string
    {
        return $this->tdClass;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function getSearchUsing(): ?Closure
    {
        return $this->searchUsing;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function getSortField(): string
    {
        return $this->sortField ?? $this->field;
    }

    public function getSortUsing(): ?Closure
    {
        return $this->sortUsing;
    }

    public function getExportUsing(): ?Closure
    {
        return $this->exportUsing;
    }

    public function isToggleable(): bool
    {
        return $this->toggleable;
    }

    public function isVisibleByDefault(): bool
    {
        return $this->visibleByDefault;
    }

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function renderValue(mixed $value, mixed $row): mixed
    {
        return $this->formatter !== null ? ($this->formatter)($value, $row) : $value;
    }

    public function exportValue(mixed $value, mixed $row): mixed
    {
        return $this->exportUsing !== null ? ($this->exportUsing)($value, $row) : $value;
    }
}
