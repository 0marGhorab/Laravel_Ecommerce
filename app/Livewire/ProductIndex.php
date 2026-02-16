<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\PromoBanner;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    public ?string $categoryFilter = null;
    public ?Category $selectedCategory = null;

    protected $queryString = ['categoryFilter' => ['except' => '']];

    public function incrementProduct(int $productId): void
    {
        $this->addToCart($productId);
    }

    public function decrementProduct(int $productId): void
    {
        $cart = Cart::current();

        $item = $cart->items()->where('product_id', $productId)->first();

        if (! $item) {
            return;
        }

        if ($item->quantity <= 1) {
            $item->delete();
        } else {
            $item->quantity--;
            $item->total_price = $item->quantity * $item->unit_price;
            $item->save();
        }

        // Clear cart cache and refresh
        Cart::clearCache();
        $cart->refresh()->load('items.product');
        $cartCount = $cart->items->sum('quantity');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $cart = \App\Models\Cart::current();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = $item->exists ? $item->quantity + 1 : 1;
        $item->unit_price = $product->price;
        $item->total_price = $item->quantity * $item->unit_price;
        $item->save();

        $cart->refresh();
        $cartCount = $cart->items->sum('quantity');

        $this->dispatch('product-added');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function mount(?string $category = null): void
    {
        // Support both 'category' and 'categoryFilter' query parameters
        if ($category && !$this->categoryFilter) {
            $this->categoryFilter = $category;
        }
        
        if ($this->categoryFilter) {
            $this->selectedCategory = Category::where('slug', $this->categoryFilter)->first();
        }
    }

    public function updatedCategoryFilter(): void
    {
        $this->selectedCategory = $this->categoryFilter 
            ? Category::where('slug', $this->categoryFilter)->first() 
            : null;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function filterByCategory(?string $categorySlug): void
    {
        $this->categoryFilter = $categorySlug;
        $this->updatedCategoryFilter();
    }

    public function clearFilter(): void
    {
        $this->categoryFilter = null;
        $this->updatedCategoryFilter();
    }

    public function addToWishlist(int $productId): void
    {
        if (! auth()->check()) {
            $this->dispatch('wishlist-login-required');
            return;
        }

        $wishlist = Wishlist::firstOrCreate(
            ['user_id' => auth()->id(), 'name' => 'Default']
        );

        $exists = $wishlist->items()->where('product_id', $productId)->exists();

        if ($exists) {
            $wishlist->items()->where('product_id', $productId)->delete();
            $this->dispatch('wishlist-removed');
        } else {
            $wishlist->items()->create(['product_id' => $productId]);
            $this->dispatch('wishlist-added');
        }

        $wishlistCount = $wishlist->items()->count();
        $this->dispatch('wishlist-updated', count: $wishlistCount);
    }

    public function render()
    {
        $query = Product::with(['category', 'images' => function($query) {
            $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
        }]);

        // Filter by category if selected
        if ($this->categoryFilter && $this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory->id);
        }

        $products = $query->orderByDesc('created_at')->paginate(12);

        $cart = Cart::current();
        $cartQuantities = $cart->items
            ->pluck('quantity', 'product_id')
            ->toArray();

        // Get wishlist product IDs for authenticated users (optimized with eager loading)
        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlist = Wishlist::with('items')
                ->where('user_id', auth()->id())
                ->where('name', 'Default')
                ->first();
            
            if ($wishlist) {
                $wishlistProductIds = $wishlist->items->pluck('product_id')->toArray();
            }
        }

        // Get all active categories (cached)
        $categories = cache()->remember('categories_list', 3600, function () {
            return Category::orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        $banners = PromoBanner::active()->ordered()->get();

        return view('livewire.product-index', [
            'products' => $products,
            'cartQuantities' => $cartQuantities,
            'wishlistProductIds' => $wishlistProductIds,
            'categories' => $categories,
            'banners' => $banners,
        ]);
    }
}
