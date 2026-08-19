<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

/**
 * Renders a set of ToolbarAction items either as one segmented control
 * (the default — same visual language as the built-in density toggle) or,
 * once dropdown() is called, as a single trigger button that opens a menu
 * listing them (same pattern as the built-in Filters/Columns buttons).
 */
final class ToolbarActionGroup
{
    private string $align = 'right';

    private ?string $cssClass = null;

    private ?string $dropdownLabel = null;

    private ?string $dropdownIcon = null;

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
     * Render as a single trigger button opening a dropdown menu of the
     * group's actions, instead of a segmented control.
     */
    public function dropdown(string $label, ?string $icon = null): static
    {
        $this->dropdownLabel = $label;
        $this->dropdownIcon = $icon;

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

    public function isDropdown(): bool
    {
        return $this->dropdownLabel !== null;
    }

    public function getDropdownLabel(): ?string
    {
        return $this->dropdownLabel;
    }

    public function getDropdownIcon(): ?string
    {
        return $this->dropdownIcon;
    }
}
