<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Optional: default test user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create Categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'sort_order' => 1,
        ]);

        $fashion = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'sort_order' => 2,
        ]);

        $home = Category::create([
            'name' => 'Home & Living',
            'slug' => 'home-living',
            'sort_order' => 3,
        ]);

        $sports = Category::create([
            'name' => 'Sports & Outdoors',
            'slug' => 'sports-outdoors',
            'sort_order' => 4,
        ]);

        // Add more categories for carousel testing
        $books = Category::create([
            'name' => 'Books & Media',
            'slug' => 'books-media',
            'sort_order' => 5,
        ]);

        $toys = Category::create([
            'name' => 'Toys & Games',
            'slug' => 'toys-games',
            'sort_order' => 6,
        ]);

        $beauty = Category::create([
            'name' => 'Beauty & Personal Care',
            'slug' => 'beauty-personal-care',
            'sort_order' => 7,
        ]);

        $health = Category::create([
            'name' => 'Health & Wellness',
            'slug' => 'health-wellness',
            'sort_order' => 8,
        ]);

        $automotive = Category::create([
            'name' => 'Automotive',
            'slug' => 'automotive',
            'sort_order' => 9,
        ]);

        $garden = Category::create([
            'name' => 'Garden & Tools',
            'slug' => 'garden-tools',
            'sort_order' => 10,
        ]);

        $pet = Category::create([
            'name' => 'Pet Supplies',
            'slug' => 'pet-supplies',
            'sort_order' => 11,
        ]);

        $office = Category::create([
            'name' => 'Office Supplies',
            'slug' => 'office-supplies',
            'sort_order' => 12,
        ]);

        $baby = Category::create([
            'name' => 'Baby & Kids',
            'slug' => 'baby-kids',
            'sort_order' => 13,
        ]);

        $food = Category::create([
            'name' => 'Food & Beverages',
            'slug' => 'food-beverages',
            'sort_order' => 14,
        ]);

        $jewelry = Category::create([
            'name' => 'Jewelry & Watches',
            'slug' => 'jewelry-watches',
            'sort_order' => 15,
        ]);

        $music = Category::create([
            'name' => 'Musical Instruments',
            'slug' => 'musical-instruments',
            'sort_order' => 16,
        ]);

        // Electronics Products
        $phone = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Smartphone Pro Max',
            'slug' => 'smartphone-pro-max',
            'sku' => 'PHN-001',
            'short_description' => 'Latest flagship smartphone with advanced features',
            'long_description' => 'Experience the future of mobile technology with our Smartphone Pro Max. Featuring a stunning 6.7-inch display, powerful processor, and professional-grade camera system.',
            'price' => 999.99,
            'sale_price' => 899.99,
            'stock_quantity' => 15,
            'weight' => 0.2,
            'width' => 7.6,
            'height' => 16.0,
            'length' => 0.8,
            'status' => 'active',
        ]);

        // Add multiple images for phone
        ProductImage::create([
            'product_id' => $phone->id,
            'path' => 'https://picsum.photos/seed/phone1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $phone->id,
            'path' => 'https://picsum.photos/seed/phone2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);
        ProductImage::create([
            'product_id' => $phone->id,
            'path' => 'https://picsum.photos/seed/phone3/800/600',
            'is_primary' => false,
            'sort_order' => 3,
        ]);

        $headphones = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Wireless Noise-Cancelling Headphones',
            'slug' => 'wireless-headphones',
            'sku' => 'HDP-001',
            'short_description' => 'Premium wireless headphones with active noise cancellation',
            'long_description' => 'Immerse yourself in crystal-clear sound with our premium wireless headphones. Features active noise cancellation, 30-hour battery life, and comfortable over-ear design.',
            'price' => 299.99,
            'stock_quantity' => 30,
            'weight' => 0.3,
            'width' => 20.0,
            'height' => 20.0,
            'length' => 18.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $headphones->id,
            'path' => 'https://picsum.photos/seed/headphone1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $headphones->id,
            'path' => 'https://picsum.photos/seed/headphone2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        $laptop = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Ultrabook Laptop 15"',
            'slug' => 'ultrabook-laptop',
            'sku' => 'LPT-001',
            'short_description' => 'Sleek and powerful laptop for professionals',
            'long_description' => 'Work efficiently with this ultra-thin laptop featuring a 15-inch display, fast SSD storage, and all-day battery life. Perfect for business and creative professionals.',
            'price' => 1299.99,
            'stock_quantity' => 12,
            'weight' => 1.5,
            'width' => 35.0,
            'height' => 24.0,
            'length' => 2.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $laptop->id,
            'path' => 'https://picsum.photos/seed/laptop1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $laptop->id,
            'path' => 'https://picsum.photos/seed/laptop2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);
        ProductImage::create([
            'product_id' => $laptop->id,
            'path' => 'https://picsum.photos/seed/laptop3/800/600',
            'is_primary' => false,
            'sort_order' => 3,
        ]);
        ProductImage::create([
            'product_id' => $laptop->id,
            'path' => 'https://picsum.photos/seed/laptop4/800/600',
            'is_primary' => false,
            'sort_order' => 4,
        ]);

        // Fashion Products
        $tshirt = Product::create([
            'category_id' => $fashion->id,
            'name' => 'Classic Cotton T-Shirt',
            'slug' => 'classic-t-shirt',
            'sku' => 'TSH-001',
            'short_description' => 'Comfortable and stylish everyday t-shirt',
            'long_description' => 'Made from 100% premium cotton, this classic t-shirt offers unmatched comfort and durability. Available in multiple colors and sizes.',
            'price' => 24.99,
            'stock_quantity' => 100,
            'weight' => 0.2,
            'width' => 30.0,
            'height' => 40.0,
            'length' => 0.5,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $tshirt->id,
            'path' => 'https://picsum.photos/seed/tshirt1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $tshirt->id,
            'path' => 'https://picsum.photos/seed/tshirt2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        $jeans = Product::create([
            'category_id' => $fashion->id,
            'name' => 'Slim Fit Denim Jeans',
            'slug' => 'slim-fit-jeans',
            'sku' => 'JNS-001',
            'short_description' => 'Modern slim fit jeans with premium denim',
            'long_description' => 'Crafted from high-quality denim, these slim fit jeans offer a modern silhouette and exceptional comfort. Perfect for casual and smart-casual occasions.',
            'price' => 79.99,
            'stock_quantity' => 45,
            'weight' => 0.6,
            'width' => 35.0,
            'height' => 100.0,
            'length' => 0.3,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $jeans->id,
            'path' => 'https://picsum.photos/seed/jeans1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $jeans->id,
            'path' => 'https://picsum.photos/seed/jeans2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);
        ProductImage::create([
            'product_id' => $jeans->id,
            'path' => 'https://picsum.photos/seed/jeans3/800/600',
            'is_primary' => false,
            'sort_order' => 3,
        ]);

        $sneakers = Product::create([
            'category_id' => $fashion->id,
            'name' => 'Premium Running Sneakers',
            'slug' => 'running-sneakers',
            'sku' => 'SNK-001',
            'short_description' => 'High-performance running shoes',
            'long_description' => 'Engineered for speed and comfort, these running sneakers feature advanced cushioning technology and breathable mesh upper for optimal performance.',
            'price' => 129.99,
            'sale_price' => 99.99,
            'stock_quantity' => 60,
            'weight' => 0.8,
            'width' => 28.0,
            'height' => 12.0,
            'length' => 30.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $sneakers->id,
            'path' => 'https://picsum.photos/seed/sneaker1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $sneakers->id,
            'path' => 'https://picsum.photos/seed/sneaker2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        // Home & Living Products
        $lamp = Product::create([
            'category_id' => $home->id,
            'name' => 'Modern Table Lamp',
            'slug' => 'modern-table-lamp',
            'sku' => 'LMP-001',
            'short_description' => 'Elegant table lamp for modern interiors',
            'long_description' => 'Add a touch of elegance to your space with this modern table lamp. Features adjustable brightness and a sleek design that complements any decor.',
            'price' => 89.99,
            'stock_quantity' => 25,
            'weight' => 1.2,
            'width' => 20.0,
            'height' => 50.0,
            'length' => 20.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $lamp->id,
            'path' => 'https://picsum.photos/seed/lamp1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $lamp->id,
            'path' => 'https://picsum.photos/seed/lamp2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);
        ProductImage::create([
            'product_id' => $lamp->id,
            'path' => 'https://picsum.photos/seed/lamp3/800/600',
            'is_primary' => false,
            'sort_order' => 3,
        ]);

        $cushion = Product::create([
            'category_id' => $home->id,
            'name' => 'Decorative Throw Cushion',
            'slug' => 'throw-cushion',
            'sku' => 'CSH-001',
            'short_description' => 'Soft and stylish decorative cushion',
            'long_description' => 'Transform your living space with this beautifully designed throw cushion. Made from premium materials for ultimate comfort and style.',
            'price' => 34.99,
            'stock_quantity' => 80,
            'weight' => 0.3,
            'width' => 40.0,
            'height' => 40.0,
            'length' => 15.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $cushion->id,
            'path' => 'https://picsum.photos/seed/cushion1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $cushion->id,
            'path' => 'https://picsum.photos/seed/cushion2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        // Sports & Outdoors Products
        $backpack = Product::create([
            'category_id' => $sports->id,
            'name' => 'Outdoor Adventure Backpack',
            'slug' => 'adventure-backpack',
            'sku' => 'BPK-001',
            'short_description' => 'Durable backpack for outdoor adventures',
            'long_description' => 'Built to withstand the elements, this adventure backpack features multiple compartments, water-resistant material, and ergonomic design for comfortable carrying.',
            'price' => 149.99,
            'stock_quantity' => 35,
            'weight' => 1.0,
            'width' => 35.0,
            'height' => 50.0,
            'length' => 20.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $backpack->id,
            'path' => 'https://picsum.photos/seed/backpack1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $backpack->id,
            'path' => 'https://picsum.photos/seed/backpack2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);
        ProductImage::create([
            'product_id' => $backpack->id,
            'path' => 'https://picsum.photos/seed/backpack3/800/600',
            'is_primary' => false,
            'sort_order' => 3,
        ]);
        ProductImage::create([
            'product_id' => $backpack->id,
            'path' => 'https://picsum.photos/seed/backpack4/800/600',
            'is_primary' => false,
            'sort_order' => 4,
        ]);

        $waterBottle = Product::create([
            'category_id' => $sports->id,
            'name' => 'Stainless Steel Water Bottle',
            'slug' => 'water-bottle',
            'sku' => 'WBT-001',
            'short_description' => 'Insulated water bottle keeps drinks cold for 24 hours',
            'long_description' => 'Stay hydrated on the go with this premium stainless steel water bottle. Double-wall insulation keeps drinks cold for 24 hours or hot for 12 hours.',
            'price' => 39.99,
            'stock_quantity' => 90,
            'weight' => 0.4,
            'width' => 8.0,
            'height' => 25.0,
            'length' => 8.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $waterBottle->id,
            'path' => 'https://picsum.photos/seed/bottle1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $waterBottle->id,
            'path' => 'https://picsum.photos/seed/bottle2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        // Add a few more products with single images
        $watch = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Smart Watch Pro',
            'slug' => 'smart-watch-pro',
            'sku' => 'WCH-001',
            'short_description' => 'Feature-rich smartwatch with health tracking',
            'long_description' => 'Monitor your health, track workouts, and stay connected with this advanced smartwatch. Features heart rate monitoring, GPS, and 7-day battery life.',
            'price' => 399.99,
            'stock_quantity' => 20,
            'weight' => 0.05,
            'width' => 4.0,
            'height' => 4.0,
            'length' => 1.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $watch->id,
            'path' => 'https://picsum.photos/seed/watch1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $jacket = Product::create([
            'category_id' => $fashion->id,
            'name' => 'Winter Parka Jacket',
            'slug' => 'winter-parka',
            'sku' => 'JKT-001',
            'short_description' => 'Warm and waterproof winter jacket',
            'long_description' => 'Stay warm and dry in any weather with this premium winter parka. Features waterproof material, insulated lining, and multiple pockets.',
            'price' => 199.99,
            'sale_price' => 149.99,
            'stock_quantity' => 40,
            'weight' => 1.2,
            'width' => 50.0,
            'height' => 70.0,
            'length' => 5.0,
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $jacket->id,
            'path' => 'https://picsum.photos/seed/jacket1/800/600',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        ProductImage::create([
            'product_id' => $jacket->id,
            'path' => 'https://picsum.photos/seed/jacket2/800/600',
            'is_primary' => false,
            'sort_order' => 2,
        ]);
    }
}
