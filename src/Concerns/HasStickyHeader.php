<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * Keeps the header row visible while a tall table scrolls — the vertical
 * counterpart to HasFrozenColumns' horizontal sticky columns.
 *
 * This can't stick relative to the page/viewport the way a naive
 * `position: sticky; top: Npx` might suggest: table_wrapper already
 * carries `overflow-x-auto` for horizontal scroll (frozen columns, wide
 * tables), and per the CSS Overflow spec, setting overflow-x to anything
 * but `visible` forces the computed overflow-y to `auto` too — the
 * wrapper silently becomes its own scroll container regardless of what
 * this trait does. A `position: sticky` cell inside it sticks relative to
 * *that* container, not the page, so a page-relative offset would never
 * actually engage. Instead, once enabled, the wrapper gets a bounded
 * height (stickyHeaderMaxHeight()) and scrolls internally on both axes —
 * the header sticks to the top of that same scrollport at `top: 0`.
 */
trait HasStickyHeader
{
    public function stickyHeader(): bool
    {
        return false;
    }

    /**
     * The table_wrapper's max-height once stickyHeader() is enabled — any
     * valid CSS length. The wrapper scrolls internally past this height,
     * with the header pinned to the top of that scroll area.
     */
    protected function stickyHeaderMaxHeight(): string
    {
        return '70vh';
    }

    public function stickyHeaderStyle(): string
    {
        if (! $this->stickyHeader()) {
            return '';
        }

        return 'position: sticky; top: 0; z-index: 10;';
    }

    public function stickyHeaderWrapperStyle(): string
    {
        if (! $this->stickyHeader()) {
            return '';
        }

        return "max-height: {$this->stickyHeaderMaxHeight()}; overflow-y: auto;";
    }
}
