<div class="dt-root rounded-xl border border-red-200 bg-red-50 p-6 text-center">
    <p class="text-sm font-medium text-red-700">{{ __('livewire-datatable::livewire-datatable.render_error') }}</p>

    @if ($debug)
        <p class="mt-2 font-mono text-xs text-red-600">[{{ $exceptionClass }}] {{ $message }}</p>
    @endif
</div>
