{{--
    Column::badges(): the first $visible labels, then "+N" whose popover lists the rest —
    on hover, or pinned open on click (Escape, scrolling or a click elsewhere closes it).
    The popover is teleported to <body> and positioned `fixed` from the trigger, so the
    table wrapper's horizontal overflow never clips it (same approach as the row actions menu).
--}}
@php
    $labels = $column->badgeLabels($value, $row);
    $visible = $column->getBadgesVisible();
    // "+1" saves no room: show everything when only one badge would be hidden.
    $shown = count($labels) <= $visible + 1 ? $labels : array_slice($labels, 0, $visible);
    $hidden = array_slice($labels, count($shown));
    $badgeVariant = $column->badgeVariant($value, $row);
    $moreClass = $this->badgeMoreClasses() ?? 'inline-flex cursor-pointer items-center rounded-full bg-[var(--dt-primary-light,#eef2ff)] px-2.5 py-0.5 text-xs font-semibold text-[var(--dt-primary,#4f46e5)] hover:brightness-95 focus:outline-none focus-visible:ring-[3px] focus-visible:ring-[var(--dt-ring,rgb(79_70_229/0.25))]';
    $popoverClass = $this->badgePopoverClasses() ?? 'fixed z-50 flex max-w-64 flex-wrap gap-1 rounded-xl border border-slate-200 bg-white p-2.5 shadow-lg';
@endphp

@if ($hidden === [])
    <span class="inline-flex flex-wrap items-center gap-1">
        @foreach ($shown as $label)
            {{ \Salioudiabate\LivewireDatatable\Support\Badge::html($label, $badgeVariant) }}
        @endforeach
    </span>
@else
    <span
        class="inline-flex flex-wrap items-center gap-1"
        x-data="{
            open: false,
            pinned: false,
            top: 0,
            left: 0,
            show() {
                const rect = this.$refs.more.getBoundingClientRect();
                this.top = rect.bottom + 6;
                this.left = Math.max(8, Math.min(rect.left, window.innerWidth - 272));
                this.open = true;
            },
            hide() {
                this.open = false;
                this.pinned = false;
            },
        }"
        x-on:scroll.window="hide()"
        x-on:keydown.escape.window="hide()"
    >
        @foreach ($shown as $label)
            {{ \Salioudiabate\LivewireDatatable\Support\Badge::html($label, $badgeVariant) }}
        @endforeach
        <button
            type="button"
            x-ref="more"
            class="{{ $moreClass }}"
            x-on:mouseenter="show()"
            x-on:mouseleave="pinned || (open = false)"
            x-on:click="pinned ? hide() : (show(), pinned = true)"
            x-bind:aria-expanded="open"
            aria-label="{{ __('livewire-datatable::livewire-datatable.badges_more', ['count' => count($hidden), 'list' => implode(', ', $hidden)]) }}"
        >+{{ count($hidden) }}</button>
        <template x-teleport="body">
            <div
                x-show="open"
                x-cloak
                x-transition.opacity.duration.100ms
                x-on:click.outside="$event.target === $refs.more || hide()"
                x-bind:style="`top: ${top}px; left: ${left}px`"
                class="{{ $popoverClass }}"
            >
                @foreach ($hidden as $label)
                    {{ \Salioudiabate\LivewireDatatable\Support\Badge::html($label, $badgeVariant) }}
                @endforeach
            </div>
        </template>
    </span>
@endif
