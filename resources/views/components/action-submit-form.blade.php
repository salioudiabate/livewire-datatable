@php
    $flattenSubmitFormData = function (array $data, string $prefix = '') use (&$flattenSubmitFormData): array {
        $pairs = [];

        $isList = array_is_list($data);

        foreach ($data as $key => $value) {
            $segment = $isList ? '' : $key;
            $name = $prefix === '' ? (string) $segment : "{$prefix}[{$segment}]";

            if (is_array($value)) {
                $pairs = [...$pairs, ...($flattenSubmitFormData)($value, $name)];
            } elseif ($value !== null) {
                $pairs[] = [$name, (string) $value];
            }
        }

        return $pairs;
    };

    $submitFormDataPairs = $flattenSubmitFormData($formData ?? []);
    $submitFormMethod = strtoupper($formMethod ?? 'POST');
    $submitFormNeedsCsrf = $submitFormMethod !== 'GET';
    $submitFormSpoofedMethod = in_array($submitFormMethod, ['PUT', 'PATCH', 'DELETE'], true) ? $submitFormMethod : null;

    $submitFormClosePrefix = ($formCloseDropdown ?? false) ? 'open = false; ' : '';
    $submitFormOnSubmit = $submitFormClosePrefix.(($formConfirm ?? null)
        ? "if (! confirm('".addslashes($formConfirm)."')) { \$event.preventDefault(); return; } submitting = true; setTimeout(() => submitting = false, 8000)"
        : 'submitting = true; setTimeout(() => submitting = false, 8000)');
@endphp

<form
    method="{{ $submitFormMethod === 'GET' ? 'GET' : 'POST' }}"
    action="{{ $formAction }}"
    @if ($formTarget ?? null)
        target="{{ $formTarget }}"
    @endif
    x-data="{ submitting: false }"
    x-on:submit="{{ $submitFormOnSubmit }}"
    class="{{ $formWrapperClass ?? 'inline-flex' }}"
>
    @if ($submitFormNeedsCsrf)
        @csrf
    @endif
    @if ($submitFormSpoofedMethod)
        <input type="hidden" name="_method" value="{{ $submitFormSpoofedMethod }}" />
    @endif
    @foreach ($submitFormDataPairs as [$name, $value])
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
    @endforeach

    <button type="submit" x-bind:disabled="submitting" class="{{ $formClass }}">
        <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span x-show="! submitting" class="inline-flex items-center gap-1.5">
            @if ($formIcon ?? null)
                {!! $formIcon !!}
            @endif
            {{ $formLabel }}
        </span>
    </button>
</form>
