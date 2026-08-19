<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * Opt-in whole-row navigation, independent of RowAction — for the common
 * "click anywhere on the row to open it" admin-table pattern instead of a
 * dedicated action button. Return null for a specific row (e.g. an
 * archived one) to leave it non-clickable while the rest of the table
 * still navigates.
 */
trait HasClickableRows
{
    public function rowUrl(mixed $row): ?string
    {
        return null;
    }
}
