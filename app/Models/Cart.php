<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    /**
     * Attributes allowed for mass assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Cart items belonging to this cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * User who owns this cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the total quantity of all products in the cart.
     */
    public function getItemsCountAttribute(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /**
     * Limit the query to active carts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'status',
            '=',
            'active',
            'and'
        );
    }
}