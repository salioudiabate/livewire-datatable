<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(TestCase::class)->in(__DIR__);

function captureStreamedResponse(StreamedResponse $response): string
{
    ob_start();
    $response->sendContent();

    return (string) ob_get_clean();
}
