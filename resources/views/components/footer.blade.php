@php
    $footerItems = $this->footer();
@endphp

@if (count($footerItems) > 0)
    @php
        $footerLeft = array_filter($footerItems, fn ($item) => ($item['align'] ?? 'right') === 'left');
        $footerRight = array_filter($footerItems, fn ($item) => ($item['align'] ?? 'right') === 'right');
    @endphp
    <div class="{{ $this->footerWrapperClasses() }}">
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($footerLeft as $item)
                <div class="{{ $this->footerItemClasses() }}">
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $item['label'] }}</span>
                    <span class="text-sm font-bold text-[var(--dt-primary,#4f46e5)]">{{ $item['value'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($footerRight as $item)
                <div class="{{ $this->footerItemClasses() }}">
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $item['label'] }}</span>
                    <span class="text-sm font-bold text-[var(--dt-primary,#4f46e5)]">{{ $item['value'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif
