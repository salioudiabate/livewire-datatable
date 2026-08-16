<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Closure;

/**
 * Declarative sugar for a per-row actions dropdown — purely additive
 * alongside the existing Column::make('Actions', ...)->view(...) pattern,
 * not a replacement for it. Either a url() resolver (plain link) or an
 * action() method name (wire:click) is expected; url() takes precedence
 * when both are set.
 */
final class RowAction
{
    private ?Closure $urlResolver = null;

    private ?string $method = null;

    /**
     * @var (Closure(mixed): bool)|null
     */
    private ?Closure $visibleResolver = null;

    private ?string $confirmMessage = null;

    private ?string $cssClass = null;

    private ?string $icon = null;

    private ?string $target = null;

    public function __construct(private readonly string $label) {}

    public static function make(string $label): static
    {
        return new self($label);
    }

    /**
     * @param  Closure(mixed $row): string  $resolver
     */
    public function url(Closure $resolver): static
    {
        $this->urlResolver = $resolver;

        return $this;
    }

    public function action(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function visible(Closure|bool $condition): static
    {
        $this->visibleResolver = $condition instanceof Closure ? $condition : fn (mixed $row): bool => $condition;

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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function target(string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getCssClass(): string
    {
        return $this->cssClass ?? '';
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function needsConfirmation(): bool
    {
        return $this->confirmMessage !== null;
    }

    public function getConfirmMessage(): ?string
    {
        return $this->confirmMessage;
    }

    public function resolveUrl(mixed $row): ?string
    {
        return $this->urlResolver !== null ? ($this->urlResolver)($row) : null;
    }

    public function isVisible(mixed $row): bool
    {
        return $this->visibleResolver === null || ($this->visibleResolver)($row);
    }
}
