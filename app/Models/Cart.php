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
     */
    public static function current(): self
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
}
