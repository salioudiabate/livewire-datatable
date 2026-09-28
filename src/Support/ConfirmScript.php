<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Support;

use Illuminate\Support\Js;

/**
 * Builds the Alpine expression behind every ->confirm() (row, bulk and toolbar actions, submit forms).
 *
 * It calls window.LivewireDatatable.confirm(message, proceed) when the app defines one — so a
 * notification package or a custom dialog can replace the browser's confirm() — and falls back
 * to the native confirm() otherwise. `proceed` runs the action; not calling it cancels it.
 */
final class ConfirmScript
{
    public static function make(string $message, string $proceed): string
    {
        return '(window.LivewireDatatable?.confirm ?? ((message, proceed) => window.confirm(message) && proceed()))('
            .Js::from($message)->toHtml().', () => { '.$proceed.' })';
    }
}
