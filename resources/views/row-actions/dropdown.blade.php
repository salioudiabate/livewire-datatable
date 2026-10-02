@if (count($actions) > 0)
    {{--
        The menu is teleported to <body> and positioned `fixed` from the trigger's bounding box:
        rendered in place, it would be clipped by the table wrapper's overflow-x-auto (and make it
        scroll) as soon as it extends past the table. Livewire resolves wire:click inside teleported
        content back to this component, so the actions keep working unchanged.
    --}}
    <div
        x-data="{
            open: false,
            style: '',
            toggle() {
                this.open ? this.close() : this.show();
            },
            show() {
                this.open = true;
                this.$nextTick(() => this.place());
            },
            close() {
                this.open = false;
            },
            place() {
                const trigger = this.$refs.trigger.getBoundingClientRect();
                const panel = this.$refs.panel;
                const gap = 4;
                const margin = 8;
                const height = panel.offsetHeight;
                const width = panel.offsetWidth;
                const fitsBelow = trigger.bottom + gap + height <= window.innerHeight - margin;
                const top = fitsBelow ? trigger.bottom + gap : Math.max(margin, trigger.top - gap - height);
                const left = Math.min(Math.max(margin, trigger.right - width), window.innerWidth - width - margin);
                this.style = `top: ${top}px; left: ${left}px;`;
            },
        }"
        x-on:keydown.escape.window="close()"
        x-on:resize.window="close()"
        x-on:scroll.window.capture="close()"
        class="relative inline-block text-left"
    >
        <button
            type="button"
            x-ref="trigger"
            x-on:click="toggle()"
            x-bind:aria-expanded="open"
            aria-haspopup="menu"
            class="rounded-lg p-1.5 text-slate-400 transition-colors duration-150 hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-[3px] focus-visible:ring-[var(--dt-ring,rgb(79_70_229/0.25))]"
        >
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
            </svg>
        </button>

        <template x-teleport="body">
        <div
            x-ref="panel"
            x-show="open"
            x-cloak
            x-bind:style="style"
            x-on:click.outside="if (! $refs.trigger.contains($event.target)) close()"
            x-on:click="close()"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            role="menu"
            class="fixed z-50 w-40 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg"
        >
            @foreach ($actions as $action)
                @if ($action->resolveUrl($row) !== null)
                    <a
                        href="{{ $action->resolveUrl($row) }}"
                        @if ($action->getTarget())
                            target="{{ $action->getTarget() }}"
                        @endif
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 {{ $action->getCssClass() }}"
                    >
                        @if ($action->getIcon())
                            {!! $action->getIcon() !!}
                        @endif
                        {{ $action->getLabel() }}
                    </a>
                @elseif ($action->resolveSubmitUrl($row) !== null)
                    @include('livewire-datatable::components.action-submit-form', [
                        'formAction' => $action->resolveSubmitUrl($row),
                        'formMethod' => $action->getSubmitMethod(),
                        'formData' => $action->resolveSubmitData($row),
                        'formTarget' => $action->getTarget(),
                        'formConfirm' => $action->getConfirmMessage(),
                        'formLabel' => $action->getLabel(),
                        'formIcon' => $action->getIcon(),
                        'formClass' => 'block w-full px-3 py-1.5 text-left text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 '.$action->getCssClass(),
                        'formCloseDropdown' => true,
                        'formWrapperClass' => 'block w-full',
                    ])
                @else
                    <button
                        type="button"
                        @if ($action->needsConfirmation())
                            x-on:click="open = false; {!! \Salioudiabate\LivewireDatatable\Support\ConfirmScript::make($action->getConfirmMessage(), '$wire.runRowAction('.\Illuminate\Support\Js::from($action->getMethod()).', '.\Illuminate\Support\Js::from($this->resolveRowKey($row)).')') !!}"
                        @else
                            wire:click="runRowAction(@js($action->getMethod()), @js($this->resolveRowKey($row)))"
                        @endif
                        class="flex w-full items-center gap-1.5 px-3 py-1.5 text-left text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 {{ $action->getCssClass() }}"
                    >
                        @if ($action->getIcon())
                            {!! $action->getIcon() !!}
                        @endif
                        {{ $action->getLabel() }}
                    </button>
                @endif
            @endforeach
        </div>
        </template>
    </div>
@endif
