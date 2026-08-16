@if (config('livewire-datatable.inject_theme_style', true))
    @php($theme = config('livewire-datatable.theme', []))
    <style>
        .dt-root {
            --dt-primary: {{ $theme['primary'] ?? '#4f46e5' }};
            --dt-primary-hover: {{ $theme['primary_hover'] ?? '#4338ca' }};
            --dt-primary-dark: {{ $theme['primary_dark'] ?? '#3730a3' }};
            --dt-primary-light: {{ $theme['primary_light'] ?? '#eef2ff' }};
            --dt-primary-text: {{ $theme['primary_text'] ?? '#ffffff' }};
        }
    </style>
@endif
