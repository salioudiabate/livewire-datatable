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

    private ?int $badgesVisible = null;

    private ?Closure $badgeLabel = null;

    private string|Closure|null $badgeVariant = null;

    private bool $toggle = false;

    private ?string $toggleAction = null;

    private ?Closure $toggleEnabled = null;

    private ?string $toggleLabel = null;

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

    /**
     * Renders a list value (array, Collection, any iterable) as badges: the first $visible ones,
     * then a "+N" badge that shows the rest in a popover on hover (pinned open on click).
     * $label turns each item into its text (e.g. fn (Role $role) => $role->name); without it,
     * items are cast to string. A format() callback, if any, runs first on the whole value.
     *
     * @param  (Closure(mixed $item, mixed $row): string)|null  $label
     */
    public function badges(int $visible = 2, ?Closure $label = null, string $variant = 'gray'): static
    {
        $this->badgesVisible = max(1, $visible);
        $this->badgeLabel = $label;
        $this->badgeVariant = $variant;
        $this->view = 'livewire-datatable::columns.badges';

        return $this;
    }

    /**
     * Renders the value (after format(), if any) as a single badge. $variant is a variant name
     * (gray, primary, success, danger, warning, info — see config classes.badge_variants) or a
     * Closure(mixed $value, mixed $row): string choosing it per row. An empty value renders nothing.
     *
     * @param  string|(Closure(mixed $value, mixed $row): string)  $variant
     */
    public function badge(string|Closure $variant = 'gray'): static
    {
        $this->badgeVariant = $variant;
        $this->view = 'livewire-datatable::columns.badge';

        return $this;
    }

    /**
     * Renders a boolean value as a switch. With $action, clicking it calls that component
     * method with the row key — through runColumnToggle(), which re-checks that the column is
     * a real toggle and that $enabled allows this row, like runRowAction() does. Without
     * $action (or when $enabled returns false) the switch is read-only. The method must
     * re-check authorization itself.
     *
     * @param  (Closure(mixed $row): bool)|null  $enabled
     */
    public function toggle(?string $action = null, ?Closure $enabled = null, ?string $label = null): static
    {
        $this->toggle = true;
        $this->toggleAction = $action;
        $this->toggleEnabled = $enabled;
        $this->toggleLabel = $label;
        $this->view = 'livewire-datatable::columns.toggle';

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

    public function badgeVariant(mixed $value, mixed $row): string
    {
        $variant = $this->badgeVariant ?? 'gray';

        return $variant instanceof Closure ? (string) $variant($value, $row) : $variant;
    }

    public function isToggle(): bool
    {
        return $this->toggle;
    }

    public function getToggleAction(): ?string
    {
        return $this->toggleAction;
    }

    public function getToggleLabel(): string
    {
        return $this->toggleLabel ?? $this->getLabel();
    }

    public function isToggleEnabled(mixed $row): bool
    {
        return $this->toggleAction !== null && ($this->toggleEnabled === null || ($this->toggleEnabled)($row));
    }

    public function isBadges(): bool
    {
        return $this->badgesVisible !== null;
    }

    public function getBadgesVisible(): int
    {
        return $this->badgesVisible ?? 2;
    }

    /**
     * @return list<string>
     */
    public function badgeLabels(mixed $value, mixed $row): array
    {
        $items = $this->renderValue($value, $row);

        if ($items === null || $items === '') {
            return [];
        }

        $items = is_iterable($items) ? $items : [$items];
        $labels = [];

        foreach ($items as $item) {
            $label = $this->badgeLabel !== null ? ($this->badgeLabel)($item, $row) : $item;

            if ($label !== null && $label !== '') {
                $labels[] = (string) $label;
            }
        }

        return $labels;
    }

    public function renderValue(mixed $value, mixed $row): mixed
    {
        return $this->formatter !== null ? ($this->formatter)($value, $row) : $value;
    }

    public function exportValue(mixed $value, mixed $row): mixed
    {
        if ($this->exportUsing !== null) {
            return ($this->exportUsing)($value, $row);
        }

        // A badges column exports every label (not only the visible ones), comma-separated.
        return $this->isBadges() ? implode(', ', $this->badgeLabels($value, $row)) : $value;
    }
}
