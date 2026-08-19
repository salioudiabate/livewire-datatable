<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Closure;

/**
 * Declarative sugar for a per-row actions dropdown — purely additive
 * alongside the existing Column::make('Actions', ...)->view(...) pattern,
 * not a replacement for it. One of url() (plain link), submit() (a real,
 * non-AJAX HTML form post — for server work that needs to hand back a
 * full response, like a per-row generated PDF opened in a new tab), or
 * action() (wire:click) is expected. Precedence when more than one is
 * set: url > submit > action.
 */
final class RowAction
{
    private ?Closure $urlResolver = null;

    private ?Closure $submitUrlResolver = null;

    private string $submitMethod = 'POST';

    /**
     * @var (Closure(mixed): array<string, mixed>)|null
     */
    private ?Closure $submitDataResolver = null;

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

    /**
     * @param  Closure(mixed $row): string  $url
     * @param  (Closure(mixed $row): array<string, mixed>)|null  $data
     */
    public function submit(Closure $url, string $method = 'POST', ?Closure $data = null, ?string $target = null): static
    {
        $this->submitUrlResolver = $url;
        $this->submitMethod = strtoupper($method);
        $this->submitDataResolver = $data;
        $this->target = $target;

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

    public function resolveSubmitUrl(mixed $row): ?string
    {
        return $this->submitUrlResolver !== null ? ($this->submitUrlResolver)($row) : null;
    }

    public function getSubmitMethod(): string
    {
        return $this->submitMethod;
    }

    /**
     * @return array<string, mixed>
     */
    public function resolveSubmitData(mixed $row): array
    {
        return $this->submitDataResolver !== null ? ($this->submitDataResolver)($row) : [];
    }

    public function isVisible(mixed $row): bool
    {
        return $this->visibleResolver === null || ($this->visibleResolver)($row);
    }
}
