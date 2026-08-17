<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * Row density (compact/comfortable/spacious) controls only the vertical
 * padding of th/td cells — composed alongside HasStyling's thClasses()/
 * tdClasses() in the header/body row views, the same way column-specific
 * classes are already composed there, rather than HasStyling knowing
 * anything about density itself.
 */
trait HasDensity
{
    public string $density = 'comfortable';

    /**
     * Livewire calls mount{TraitName}() on every trait automatically (see
     * SupportLifecycleHooks) — the same mechanism HasColumnVisibility uses
     * for its own session-persisted state.
     */
    public function mountHasDensity(): void
    {
        $sessionKey = $this->densitySessionKey();

        if ($sessionKey !== null && session()->has($sessionKey)) {
            /** @var string $density */
            $density = session($sessionKey);
            $this->density = $density;

            return;
        }

        $this->density = $this->defaultDensity();
    }

    public function showDensityToggle(): bool
    {
        return true;
    }

    /**
     * @return array<int, string>
     */
    public function densityOptions(): array
    {
        return ['compact', 'comfortable', 'spacious'];
    }

    public function setDensity(string $density): void
    {
        if (! in_array($density, $this->densityOptions(), true)) {
            return;
        }

        $this->density = $density;

        if ($sessionKey = $this->densitySessionKey()) {
            session([$sessionKey => $density]);
        }
    }

    public function densityThClasses(): string
    {
        return (string) config("livewire-datatable.density.th.{$this->density}", '');
    }

    public function densityTdClasses(): string
    {
        return (string) config("livewire-datatable.density.td.{$this->density}", '');
    }

    /**
     * Optional session key prefix to persist the density choice across
     * requests, mirroring persistColumnVisibility(). Returning null (the
     * default) keeps it request-local.
     */
    protected function persistDensity(): ?string
    {
        return null;
    }

    protected function defaultDensity(): string
    {
        return (string) config('livewire-datatable.density.default', 'comfortable');
    }

    private function densitySessionKey(): ?string
    {
        $key = $this->persistDensity();

        return $key === null ? null : "livewire-datatable.density.{$key}";
    }
}
