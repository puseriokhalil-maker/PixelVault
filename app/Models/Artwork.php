<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'artist_id',
    'slug',
    'title',
    'description',
    'image_path',
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

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->image_path === null) {
                return null;
            }

            if (Str::startsWith($this->image_path, 'storage/')) {
                return Storage::disk('public')->url(Str::after($this->image_path, 'storage/'));
            }

            return asset($this->image_path);
        });
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
