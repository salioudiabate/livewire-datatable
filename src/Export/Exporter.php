<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Export;

use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Symfony\Component\HttpFoundation\Response;

interface Exporter
{
    /**
     * Return type is the common Symfony Response base rather than
     * StreamedResponse specifically: a true streaming exporter (CSV) can
     * return a StreamedResponse, but a spreadsheet-format exporter (Excel)
     * has to write a complete file before it can respond and returns a
     * BinaryFileResponse instead — both are Response subclasses.
     *
     * @param  array<int, Column>  $columns
     */
    public function export(DataSource $dataSource, array $columns, string $filename): Response;
}
