<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Export;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Contracts\Support\Htmlable;
use JsonSerializable;
use Stringable;
use Traversable;
use UnitEnum;

/**
 * Turns any cell value into something a CSV/Excel/PDF cell can hold: a string, a number or null.
 * Exports use the raw column value (not the display format()), which is often an object — an
 * enum cast, a date cast, an Htmlable… — that cannot be cast to string and used to crash the
 * export ("Object of class … could not be converted to string").
 */
final class ExportValue
{
    public static function normalize(mixed $value): string|int|float|null
    {
        return match (true) {
            $value === null, is_string($value), is_int($value), is_float($value) => $value,
            is_bool($value) => $value ? '1' : '0',
            // An enum exports its human label when it has one (label()), else its value / name.
            $value instanceof UnitEnum => method_exists($value, 'label') ? (string) $value->label() : ($value instanceof BackedEnum ? $value->value : $value->name),
            // A date-only value (midnight) exports without a time.
            $value instanceof DateTimeInterface => $value->format($value->format('H:i:s') === '00:00:00' ? 'Y-m-d' : 'Y-m-d H:i:s'),
            // Before Stringable: a Collection is both iterable and Stringable (JSON).
            is_iterable($value) => implode(', ', array_filter(
                array_map(fn (mixed $item) => (string) (self::normalize($item) ?? ''), $value instanceof Traversable ? iterator_to_array($value, false) : $value),
                fn (string $item) => $item !== '',
            )),
            $value instanceof Htmlable => self::text($value->toHtml()),
            $value instanceof Stringable => self::text((string) $value),
            $value instanceof JsonSerializable => (string) json_encode($value, JSON_UNESCAPED_UNICODE),
            default => '',
        };
    }

    private static function text(string $markup): string
    {
        return trim(html_entity_decode(strip_tags($markup), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
