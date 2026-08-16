<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Illuminate\Database\QueryException;

/**
 * Driver-aware, verified against real PDO error output (not guessed):
 * Postgres reports foreign key violations under the distinct SQLSTATE
 * 23503; MySQL *and* SQLite both report them under the generic 23000
 * integrity-constraint SQLSTATE shared with unrelated violations (UNIQUE,
 * NOT NULL), so the message itself is the only reliable signal there —
 * confirmed empirically for SQLite: errorInfo = ['23000', 19, 'FOREIGN KEY
 * constraint failed']. The predecessor implementations this package
 * replaces only checked Postgres/MySQL-style codes, which would have
 * silently misclassified every SQLite failure — the exact environment this
 * package's own test suite (and Testbench in general) runs under.
 */
trait DetectsForeignKeyViolations
{
    protected function isForeignKeyViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        if ($sqlState === '23503') {
            return true;
        }

        return str_contains(strtolower($exception->getMessage()), 'foreign key');
    }
}
