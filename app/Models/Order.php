<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending',
        'partially_paid',
        'completed',
        'cancelled',
    ];

    /**
     * Attributes allowed for mass assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'shipping_address',
        'voucher_code',
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
     * User who placed this order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Items belonging to this order.
     *
     * Keep this relationship because the checkout controller and views
     * currently use $order->items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Backward-compatible alias for items().
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Payments recorded for this order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate the amount already paid.
     */
    public function paidAmount(?int $excludePaymentId = null): float
    {
        $query = $this->payments();

        if ($excludePaymentId !== null) {
            $query->where(
                'id',
                '!=',
                $excludePaymentId,
                'and'
            );
        }

        return round(
            (float) $query->sum('amount'),
            2
        );
    }

    /**
     * Calculate the remaining unpaid balance.
     */
    public function remainingBalance(
        ?int $excludePaymentId = null
    ): float {
        $total = (float) $this->total_amount;
        $paid = $this->paidAmount($excludePaymentId);

        return round(
            max(0, $total - $paid),
            2
        );
    }

    /**
     * Determine whether this order is cancelled.
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Determine whether this order is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }
}