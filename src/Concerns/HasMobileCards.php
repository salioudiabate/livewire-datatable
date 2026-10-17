<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\Column;

/**
 * On a phone, a wide table only reads by scrolling sideways. Below
 * mobileCardsBreakpoint() pixels each row is laid out as a card instead:
 * the first column becomes the card's title, every other cell shows its
 * column label on the left and its value on the right, the selection
 * checkbox and the row-actions menu sit in the card's corners. Pure CSS
 * (theme-style.blade.php): the same markup serves both layouts, each cell
 * carrying its label in data-label. The header row is hidden in card mode.
 */
trait HasMobileCards
{
    public function mobileCards(): bool
    {
        return (bool) config('livewire-datatable.mobile_cards', true);
    }

    /**
     * Width, in CSS pixels, below which rows become cards.
     */
    public function mobileCardsBreakpoint(): int
    {
        return (int) config('livewire-datatable.mobile_cards_breakpoint', 640);
    }

    /**
     * Column shown as the card's title: the one marked cardTitle(), else the first.
     *
     * @param  array<int, Column>  $columns
     */
    public function cardTitleColumn(array $columns): ?Column
    {
        foreach ($columns as $column) {
            if ($column->isCardTitle()) {
                return $column;
            }
        }

        $first = reset($columns);

        return $first === false ? null : $first;
    }
}
