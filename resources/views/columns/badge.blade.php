{{-- Column::badge(): the value (after format()) as one badge; nothing when empty. --}}
@php($badgeLabel = $column->renderValue($value, $row))
@if ($badgeLabel !== null && $badgeLabel !== '')
    {{ \Salioudiabate\LivewireDatatable\Support\Badge::html((string) $badgeLabel, $column->badgeVariant($value, $row)) }}
@endif
