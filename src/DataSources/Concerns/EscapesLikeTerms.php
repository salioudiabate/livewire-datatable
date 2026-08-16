<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources\Concerns;

trait EscapesLikeTerms
{
    /**
     * Escapes SQL LIKE metacharacters (%, _, \) in a user-supplied search
     * term, so e.g. typing "%" doesn't match every row and "_" doesn't
     * match an arbitrary single character.
     */
    protected function escapeLikeTerm(string $term): string
    {
        return addcslashes($term, '%_\\');
    }
}
