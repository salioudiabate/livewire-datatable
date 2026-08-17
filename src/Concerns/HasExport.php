<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Illuminate\Support\Str;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\Export\CsvExporter;
use Salioudiabate\LivewireDatatable\Export\Exporter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exports the current filtered view (search + filters applied, matching
 * what's on screen) but not the current page's slice — the whole matching
 * set, streamed in chunks rather than materialized all at once.
 */
trait HasExport
{
    abstract protected function filteredDataSource(): DataSource;

    /**
     * @return array<int, Column>
     */
    abstract public function visibleColumns(): array;

    public function showExport(): bool
    {
        return true;
    }

    public function export(): Response
    {
        return $this->exporter()->export(
            $this->filteredDataSource(),
            $this->exportColumns(),
            $this->exportFilename()
        );
    }

    protected function exporter(): Exporter
    {
        return new CsvExporter((int) config('livewire-datatable.export.chunk_size', 1000));
    }

    /**
     * @return array<int, Column>
     */
    protected function exportColumns(): array
    {
        return $this->visibleColumns();
    }

    protected function exportFilename(): string
    {
        return Str::kebab(class_basename(static::class)).'-'.now()->format('Y-m-d-His').'.csv';
    }
}
