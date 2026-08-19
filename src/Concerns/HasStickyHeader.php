<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * Keeps the header row visible while a tall table scrolls vertically — the
 * vertical counterpart to HasFrozenColumns' horizontal sticky columns. Off
 * by default: it only matters once a table's rows exceed one screen, and
 * stacking it against a host app's own fixed navbar without an offset is a
 * real footgun, which is exactly what stickyHeaderOffset() prevents.
 */
trait HasStickyHeader
{
    public function stickyHeader(): bool
    {
        return false;
    }

    /**
     * Pixels to offset the sticky header from the top of the scrolling
     * context — set this to the height of your app's own fixed/sticky
     * navbar so the two don't overlap.
     */
    protected function stickyHeaderOffset(): int
    {
        return 0;
    }

    public function stickyHeaderStyle(): string
    {
        if (! $this->stickyHeader()) {
            return '';
        }

        return "position: sticky; top: {$this->stickyHeaderOffset()}px; z-index: 10;";
    }
}
