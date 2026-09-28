<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Support;

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

/**
 * One badge as HTML, for format() callbacks composing richer cells (a badge next to other
 * text) — same markup and classes as Column::badge(). The label is escaped.
 */
final class Badge
{
    public static function html(string $label, string $variant = 'gray'): HtmlString
    {
        return new HtmlString(View::make('livewire-datatable::columns.partials.badge', [
            'label' => $label,
            'variant' => $variant,
            'classes' => self::classes($variant),
        ])->render());
    }

    /**
     * config classes.badge_variants.{variant}, or null for the package default — which lives in
     * the partial view, not here, so a Tailwind scan of the package views picks it up.
     */
    private static function classes(string $variant): ?string
    {
        $classes = config('livewire-datatable.classes.badge_variants.'.$variant);

        return is_string($classes) ? $classes : null;
    }
}
