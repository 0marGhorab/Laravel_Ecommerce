<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'status',
    ];

    /**
     * Get or create the current active cart for the visitor (user or guest).
     * Cached per request to avoid multiple database queries.
     */
    public static function current(): self
    {
        $cacheKey = 'cart_' . (auth()->id() ?? session()->getId());
        
        return app()->bound($cacheKey) 
            ? app($cacheKey)
            : app()->instance($cacheKey, static::resolveCurrentCart());
    }

    /**
     * Resolve the current cart from database.
     */
    protected static function resolveCurrentCart(): self
    {
        $query = static::query();

        if (auth()->check()) {
            $cart = $query->firstOrCreate(
                ['user_id' => auth()->id(), 'status' => 'active'],
                ['session_id' => session()->getId()]
            );
        } else {
            $cart = $query->firstOrCreate(
                ['session_id' => session()->getId(), 'status' => 'active'],
                ['user_id' => null]
            );
        }

        return $cart->load('items.product');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total quantity of items in the cart using database aggregate.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items()->sum('quantity') ?? 0;
    }

    /**
     * Clear the cached cart instance for the current user/session.
     */
    public static function clearCache(): void
    {
        $cacheKey = 'cart_' . (auth()->id() ?? session()->getId());
        app()->forgetInstance($cacheKey);
    }
}
