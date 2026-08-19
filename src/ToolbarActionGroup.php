<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

/**
 * Renders a set of ToolbarAction items as one segmented control (the same
 * visual language as the built-in density toggle) instead of separate
 * standalone buttons.
 */
final class ToolbarActionGroup
{
    private string $align = 'right';

    private ?string $cssClass = null;

    /**
     * @param  array<int, ToolbarAction>  $actions
     */
    public function __construct(private readonly array $actions) {}

    /**
     * @param  array<int, ToolbarAction>  $actions
     */
    public static function make(array $actions): static
    {
        return new self($actions);
    }

    public function align(string $align): static
    {
        $this->align = $align === 'left' ? 'left' : 'right';

        return $this;
    }

    public function cssClass(string $class): static
    {
        $this->cssClass = $class;

        return $this;
    }

    /**
     * @return array<int, ToolbarAction>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getAlign(): string
    {
        return $this->align;
    }

    public function getCssClass(): string
    {
        return $this->cssClass ?? '';
    }
}
