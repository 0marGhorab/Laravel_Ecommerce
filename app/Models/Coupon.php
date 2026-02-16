<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'times_used',
        'uses_per_user',
        'starts_at',
        'expires_at',
        'active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Find a valid coupon by code for the given subtotal and optional user.
     * Returns the coupon or null; errors can be read from the optional $message parameter.
     */
    public static function findValid(
        string $code,
        float $subtotal,
        ?int $userId = null,
        ?string &$message = null
    ): ?self {
        $coupon = self::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            $message = 'Invalid coupon code.';
            return null;
        }

        if (!$coupon->active) {
            $message = 'This coupon is no longer active.';
            return null;
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $message = 'This coupon is not yet valid.';
            return null;
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $message = 'This coupon has expired.';
            return null;
        }

        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            $message = 'Minimum order amount for this coupon is $' . number_format($coupon->min_order_amount, 2) . '.';
            return null;
        }

        if ($coupon->max_uses !== null && $coupon->times_used >= $coupon->max_uses) {
            $message = 'This coupon has reached its usage limit.';
            return null;
        }

        if ($userId !== null && $coupon->uses_per_user !== null) {
            $userUses = Order::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
            if ($userUses >= $coupon->uses_per_user) {
                $message = 'You have already used this coupon.';
                return null;
            }
        }

        $message = null;
        return $coupon;
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $value = min(100, max(0, (float) $this->value));
            return round($subtotal * ($value / 100), 2);
        }

        // fixed
        $off = max(0, (float) $this->value);
        return round(min($off, $subtotal), 2);
    }
}
