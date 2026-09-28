{{--
    One badge. $classes (config classes.badge_variants.{variant}) wins; otherwise the package
    default for $variant — written here in full so Tailwind's scan of the package views keeps them.
--}}
@php
    $badgeDefaults = [
        'gray' => 'inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600',
        'primary' => 'inline-flex items-center rounded-full bg-[var(--dt-primary-light,#eef2ff)] px-2.5 py-0.5 text-xs font-medium text-[var(--dt-primary-dark,#3730a3)]',
        'success' => 'inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700',
        'danger' => 'inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700',
        'warning' => 'inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700',
        'info' => 'inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700',
    ];
@endphp
<span class="{{ $classes ?? $badgeDefaults[$variant] ?? $badgeDefaults['gray'] }}">{{ $label }}</span>
