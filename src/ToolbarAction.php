<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Closure;
use Illuminate\Support\Facades\Gate;

/**
 * A custom toolbar button, independent of the per-row (RowAction) and
 * bulk-selection (BulkAction) actions the package already ships. Exactly
 * one trigger is expected: url() (plain link), dispatch() (fires a
 * Livewire event — e.g. $dispatch('openModal', [...]) to open a host
 * app's own modal system), action() (a wire:click method call on this
 * component), or submit() (a real, non-AJAX HTML form post — for server
 * work Livewire's own AJAX cycle can't carry, like streaming a generated
 * PDF back as the response of a new tab). Precedence when more than one
 * trigger is set: url > dispatch > submit > action.
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

    private ?string $submitAction = null;

    private string $submitMethod = 'POST';

    /**
     * @var array<string, mixed>|Closure(): array<string, mixed>
     */
    private array|Closure $submitData = [];

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

    /**
     * A real HTML form submission (not Livewire AJAX) — the only way for
     * server-side work to send back a full HTTP response the browser can
     * navigate to or open in a new tab, e.g. generating a PDF and
     * rendering it in target: '_blank'. $data may be a closure so it's
     * resolved against the component's current state (filters, search,
     * selection...) at render time, not just once when the action is
     * declared.
     *
     * @param  array<string, mixed>|Closure(): array<string, mixed>  $data
     */
    public function submit(string $action, string $method = 'POST', array|Closure $data = [], ?string $target = null): static
    {
        $this->submitAction = $action;
        $this->submitMethod = strtoupper($method);
        $this->submitData = $data;
        $this->target = $target;

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

    public function getSubmitAction(): ?string
    {
        return $this->submitAction;
    }

    public function getSubmitMethod(): string
    {
        return $this->submitMethod;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSubmitData(): array
    {
        return $this->submitData instanceof Closure ? ($this->submitData)() : $this->submitData;
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
     * One of 'url', 'dispatch', 'submit', 'action', or 'none' — determines
     * which wiring the toolbar view renders.
     */
    public function getTrigger(): string
    {
        return match (true) {
            $this->url !== null => 'url',
            $this->dispatchEvent !== null => 'dispatch',
            $this->submitAction !== null => 'submit',
            $this->method !== null => 'action',
            default => 'none',
        };
    }

    public function isAuthorized(): bool
    {
        return $this->permission === null || Gate::allows($this->permission);
    }
}
