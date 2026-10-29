<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'artist_id',
    'slug',
    'title',
    'description',
    'category',
    'price_amount',
    'currency',
    'is_test_listing',
    'is_published',
])]
class Artwork extends Model
{
    protected function casts(): array
    {
        return [
            'price_amount' => 'integer',
            'is_test_listing' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
