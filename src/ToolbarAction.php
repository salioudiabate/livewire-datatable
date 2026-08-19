<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Illuminate\Support\Facades\Gate;

/**
 * A custom toolbar button, independent of the per-row (RowAction) and
 * bulk-selection (BulkAction) actions the package already ships. Exactly
 * one trigger is expected: url() (plain link), dispatch() (fires a
 * Livewire event — e.g. $dispatch('openModal', [...]) to open a host
 * app's own modal system), or action() (a wire:click method call on this
 * component). Precedence when more than one is set: url > dispatch > action.
 */
final class ToolbarAction
{
    private ?string $url = null;

    private ?string $target = null;

    private ?string $dispatchEvent = null;

    /**
     * @var array<string, mixed>
     */
    private array $dispatchParams = [];

    private ?string $method = null;

    private ?string $confirmMessage = null;

    private ?string $cssClass = null;

    private ?string $icon = null;

    private ?string $permission = null;

    private string $align = 'right';

    public function __construct(private readonly string $label) {}

    public static function make(string $label): static
    {
        return new self($label);
    }

    public function url(string $url, ?string $target = null): static
    {
        $this->url = $url;
        $this->target = $target;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $params
     */
    public function dispatch(string $event, array $params = []): static
    {
        $this->dispatchEvent = $event;
        $this->dispatchParams = $params;

        return $this;
    }

    public function action(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function confirm(string $message): static
    {
        $this->confirmMessage = $message;

        return $this;
    }

    public function cssClass(string $class): static
    {
        $this->cssClass = $class;

        return $this;
    }

    /**
     * Raw inline SVG markup (or any HTML), rendered unescaped before the
     * label — the package has no icon-name registry, so you bring your own
     * markup, the same way every built-in toolbar button does.
     */
    public function icon(string $svg): static
    {
        $this->icon = $svg;

        return $this;
    }

    public function permission(string $ability): static
    {
        $this->permission = $ability;

        return $this;
    }

    public function align(string $align): static
    {
        $this->align = $align === 'left' ? 'left' : 'right';

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getDispatchEvent(): ?string
    {
        return $this->dispatchEvent;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDispatchParams(): array
    {
        return $this->dispatchParams;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function needsConfirmation(): bool
    {
        return $this->confirmMessage !== null;
    }

    public function getConfirmMessage(): ?string
    {
        return $this->confirmMessage;
    }

    public function getCssClass(): string
    {
        return $this->cssClass ?? '';
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getAlign(): string
    {
        return $this->align;
    }

    /**
     * One of 'url', 'dispatch', 'action', or 'none' — determines which
     * wiring the toolbar view renders.
     */
    public function getTrigger(): string
    {
        return match (true) {
            $this->url !== null => 'url',
            $this->dispatchEvent !== null => 'dispatch',
            $this->method !== null => 'action',
            default => 'none',
        };
    }

    public function isAuthorized(): bool
    {
        return $this->permission === null || Gate::allows($this->permission);
    }
}
