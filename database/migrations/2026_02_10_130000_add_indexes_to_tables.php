<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Carts table indexes
        Schema::table('carts', function (Blueprint $table) {
            // Composite index for user_id + status (most common query)
            $table->index(['user_id', 'status'], 'carts_user_status_index');
            // Composite index for session_id + status (guest carts)
            $table->index(['session_id', 'status'], 'carts_session_status_index');
        });

        // Cart items table indexes
        Schema::table('cart_items', function (Blueprint $table) {
            // Index for cart_id lookups
            $table->index('cart_id', 'cart_items_cart_id_index');
            // Composite index for cart_id + product_id (checking if item exists)
            $table->index(['cart_id', 'product_id'], 'cart_items_cart_product_index');
        });

        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            // Index for category_id filtering
            $table->index('category_id', 'products_category_id_index');
            // Composite index for category_id + status (common filter)
            $table->index(['category_id', 'status'], 'products_category_status_index');
            // Index for slug lookups
            $table->index('slug', 'products_slug_index');
        });

        // Product images table indexes
        Schema::table('product_images', function (Blueprint $table) {
            // Composite index for product_id + is_primary + sort_order (common ordering)
            $table->index(['product_id', 'is_primary', 'sort_order'], 'product_images_ordering_index');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            // Composite index for user_id + created_at (order history queries)
            $table->index(['user_id', 'created_at'], 'orders_user_created_index');
            // Index for order_number lookups
            $table->index('order_number', 'orders_order_number_index');
        });

        // Order items table indexes
        Schema::table('order_items', function (Blueprint $table) {
            // Index for order_id lookups
            $table->index('order_id', 'order_items_order_id_index');
        });

        // Wishlists table indexes
        Schema::table('wishlists', function (Blueprint $table) {
            // Composite index for user_id + name (common query)
            $table->index(['user_id', 'name'], 'wishlists_user_name_index');
        });

        // Wishlist items table indexes
        Schema::table('wishlist_items', function (Blueprint $table) {
            // Composite index for wishlist_id + product_id (checking if item exists)
            $table->index(['wishlist_id', 'product_id'], 'wishlist_items_wishlist_product_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_status_index');
            $table->dropIndex('carts_session_status_index');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_id_index');
            $table->dropIndex('cart_items_cart_product_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_category_status_index');
            $table->dropIndex('products_slug_index');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex('product_images_ordering_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_created_index');
            $table->dropIndex('orders_order_number_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_id_index');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('wishlists_user_name_index');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropIndex('wishlist_items_wishlist_product_index');
        });
    }
};
