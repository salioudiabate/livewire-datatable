<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $table = 'dt_test_posts';

    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'dt_test_author_id');
    }
}
