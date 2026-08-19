<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Illuminate\Support\Facades\Gate;

/**
 * Unifies what the audited predecessor projects split across three parallel
 * arrays (bulkActions()/bulkActionsConfirm()/bulkActionsConfirmClass()) into
 * one declarative, fluent object — and adds permission() so authorization is
 * checked centrally (see Concerns\HasBulkActions::runBulkAction()) instead
 * of relying solely on the button not being rendered. By default the
 * button dispatches through wire:click (see runBulkAction()); call
 * submit() to render it as a real, non-AJAX HTML form post instead — for
 * server work that needs to hand back a full response, like exporting the
 * current selection as a PDF opened in a new tab.
 */
final class BulkAction
{
    private ?string $confirmMessage = null;

    private ?string $cssClass = null;

    private ?string $icon = null;

    private ?string $permission = null;

    private ?string $submitAction = null;

    private string $submitMethod = 'POST';

    /**
     * @var array<string, mixed>
     */
    private array $submitData = [];

    private ?string $target = null;

    public function __construct(
        private readonly string $method,
        private readonly string $label,
    ) {}

    public static function make(string $method, string $label): static
    {
        return new self($method, $label);
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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function permission(string $ability): static
    {
        $this->permission = $ability;

        return $this;
    }

    /**
     * The selected keys are always sent as selected[] hidden inputs
     * alongside $data — no need to include them yourself.
     *
     * @param  array<string, mixed>  $data
     */
    public function submit(string $action, string $method = 'POST', array $data = [], ?string $target = null): static
    {
        $this->submitAction = $action;
        $this->submitMethod = strtoupper($method);
        $this->submitData = $data;
        $this->target = $target;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getConfirmMessage(): ?string
    {
        return $this->confirmMessage;
    }

    public function needsConfirmation(): bool
    {
        return $this->confirmMessage !== null;
    }

    public function getCssClass(): string
    {
        return $this->cssClass ?? '';
    }

    public function getIcon(): ?string
    {
        return $this->icon;
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
        return $this->submitData;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function isAuthorized(): bool
    {
        return $this->permission === null || Gate::allows($this->permission);
    }
}
