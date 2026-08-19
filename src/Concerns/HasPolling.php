<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * Opt-in auto-refresh for dashboard-style tables — wires Livewire's own
 * wire:poll onto the root element rather than reinventing polling.
 */
trait HasPolling
{
    /**
     * Milliseconds between automatic refreshes, or null (the default) to
     * disable polling entirely.
     */
    public function pollInterval(): ?int
    {
        return null;
    }
}
