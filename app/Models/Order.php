<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'artist_id',
    'status',
    'subtotal_amount',
    'platform_fee_amount',
    'total_amount',
    'currency',
    'completed_at',
    'paid_at',
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'subtotal_amount' => 'integer',
            'platform_fee_amount' => 'integer',
            'total_amount' => 'integer',
            'completed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
