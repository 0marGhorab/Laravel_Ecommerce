<?php

namespace Database\Seeders;

use App\Models\PromoBanner;
use Illuminate\Database\Seeder;

class PromoBannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Summer Sale — 30% Off',
                'subtitle' => 'Use code SUMMER30 at checkout',
                'cta_text' => 'Shop now',
                'cta_url' => '/',
                'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&h=400&fit=crop',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'title' => 'Free Shipping Over $100',
                'subtitle' => 'On all orders, no minimum',
                'cta_text' => 'View products',
                'cta_url' => '/',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&h=400&fit=crop',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'New Arrivals',
                'subtitle' => 'Fresh styles just dropped',
                'cta_text' => 'Explore',
                'cta_url' => '/',
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&h=400&fit=crop',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            PromoBanner::create($banner);
        }
    }
}
