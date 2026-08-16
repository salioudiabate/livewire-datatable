<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Export;

use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface Exporter
{
    /**
     * @param  array<int, Column>  $columns
     */
    public function export(DataSource $dataSource, array $columns, string $filename): StreamedResponse;
}
