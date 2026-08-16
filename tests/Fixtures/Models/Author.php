<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = 'dt_test_authors';

    protected $guarded = [];

    public $timestamps = false;
}
