<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * A banner that appears while the browser is offline and disappears once
 * back online — pure Livewire, no custom JS: `wire:offline` is hidden via
 * a global `display: none` CSS rule Livewire itself injects, then toggled
 * by listening to the native `offline`/`online` window events.
 */
trait HasOfflineIndicator
{
    public function showOfflineIndicator(): bool
    {
        return true;
    }
}
