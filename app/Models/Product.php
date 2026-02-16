<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'long_description',
        'price',
        'sale_price',
        'stock_quantity',
        'weight',
        'width',
        'height',
        'length',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('approved', true);
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->approvedReviews()->avg('rating');
        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }
}
