<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Illuminate\Support\Facades\Gate;

/**
 * Unifies what the audited predecessor projects split across three parallel
 * arrays (bulkActions()/bulkActionsConfirm()/bulkActionsConfirmClass()) into
 * one declarative, fluent object — and adds permission() so authorization is
 * checked centrally (see Concerns\HasBulkActions::runBulkAction()) instead
 * of relying solely on the button not being rendered.
 */
final class BulkAction
{
    private ?string $confirmMessage = null;

    private ?string $cssClass = null;

    private ?string $icon = null;

    private ?string $permission = null;

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

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function isAuthorized(): bool
    {
        return $this->permission === null || Gate::allows($this->permission);
    }
}
