<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Illuminate\Support\Str;

/**
 * Uses Livewire's queryString() method rather than the #[Url] attribute:
 * attribute arguments must be compile-time constants, so a per-instance
 * dynamic alias (needed to avoid two tables on the same page colliding in
 * the query string) is not achievable with #[Url] at all. queryString()
 * is evaluated per-request with full access to $this, and remains a
 * first-class Livewire mechanism alongside the attribute form.
 */
trait HasUrlBinding
{
    /**
     * Opt out of URL binding entirely — useful for embedded/modal tables
     * where polluting the page URL isn't desirable.
     */
    protected function withoutUrlBinding(): bool
    {
        return false;
    }

    /**
     * Default query-string prefix, derived from the component's class name.
     * Must be overridden when two instances of the *same* table class are
     * rendered on one page, since they would otherwise share this default
     * and collide.
     */
    protected function urlKey(): string
    {
        return Str::kebab(class_basename(static::class));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function queryString(): array
    {
        if ($this->withoutUrlBinding()) {
            return [];
        }

        $prefix = $this->urlKey();

        return [
            'search' => ['as' => "{$prefix}-q", 'except' => ''],
            'filterValues' => ['as' => "{$prefix}-f", 'except' => []],
            'sortField' => ['as' => "{$prefix}-sort", 'except' => null],
            'sortDirection' => ['as' => "{$prefix}-dir", 'except' => 'asc'],
            'page' => ['as' => "{$prefix}-page", 'except' => 1],
            'perPage' => ['as' => "{$prefix}-per", 'except' => $this->perPage],
        ];
    }
}
