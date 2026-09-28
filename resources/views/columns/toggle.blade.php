{{--
    Column::toggle(): a switch for a boolean value. Clickable only when the column has an
    action and toggle()'s $enabled allows this row; the click goes through runColumnToggle(),
    which re-checks both server-side before calling the action with the row key.
--}}
@php
    $toggleOn = (bool) $value;
    $toggleEnabled = $column->isToggleEnabled($row);
    $toggleClass = $this->toggleClasses() ?? 'inline-flex h-6 w-10 items-center rounded-full bg-slate-200 p-[3px] transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] focus:ring-offset-1 disabled:cursor-default disabled:opacity-55 enabled:cursor-pointer';
    $toggleOnClass = $this->toggleOnClasses() ?? 'justify-end bg-[var(--dt-primary,#4f46e5)]';
    $toggleKnobClass = $this->toggleKnobClasses() ?? 'block h-[18px] w-[18px] rounded-full bg-white shadow';
@endphp
<button
    type="button"
    role="switch"
    aria-checked="{{ $toggleOn ? 'true' : 'false' }}"
    aria-label="{{ $column->getToggleLabel() }}"
    class="{{ trim($toggleClass.' '.($toggleOn ? $toggleOnClass : '')) }}"
    @if ($toggleEnabled)
        wire:click="runColumnToggle(@js($column->getField()), @js($this->resolveRowKey($row)))"
        wire:loading.attr="disabled"
    @else
        disabled
    @endif
><span class="{{ $toggleKnobClass }}"></span></button>
