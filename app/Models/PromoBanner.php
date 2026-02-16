<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoBanner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'cta_text',
        'cta_url',
        'image',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }
        return asset('storage/' . $this->image);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
