<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'path',
        'is_primary',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute(): string
    {
        // If path already starts with http, return as is
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        // If path starts with storage/, use asset helper
        if (str_starts_with($this->path, 'storage/')) {
            return asset($this->path);
        }

        // Otherwise, assume it's in storage/app/public
        return asset('storage/' . $this->path);
    }
}
