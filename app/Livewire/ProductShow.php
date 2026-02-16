<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Cart;
use Livewire\Component;

class ProductShow extends Component
{
    public string $slug;
    public Product $product;

    /** Review form (when can review) */
    public int $rating = 5;
    public string $reviewTitle = '';
    public string $reviewBody = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function addToCart(): void
    {
        $product = $this->product;

        $cart = \App\Models\Cart::current();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = $item->exists ? $item->quantity + 1 : 1;
        $item->unit_price = $product->price;
        $item->total_price = $item->quantity * $item->unit_price;
        $item->save();

        Cart::clearCache();
        $cart->refresh()->load('items.product');
        $cartCount = $cart->items->sum('quantity');

        $this->dispatch('product-added');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function incrementProduct(): void
    {
        $this->addToCart();
    }

    public function decrementProduct(): void
    {
        $cart = Cart::current();

        $item = $cart->items()->where('product_id', $this->product->id)->first();

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

        Cart::clearCache();
        $cart->refresh()->load('items.product');
        $cartCount = $cart->items->sum('quantity');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function submitReview(): void
    {
        if (! auth()->check()) {
            $this->dispatch('review-login-required');
            return;
        }

        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'reviewTitle' => ['nullable', 'string', 'max:255'],
            'reviewBody' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.required' => 'Please select a rating.',
            'rating.min' => 'Rating must be between 1 and 5.',
            'rating.max' => 'Rating must be between 1 and 5.',
        ]);

        $existing = Review::where('user_id', auth()->id())
            ->where('product_id', $this->product->id)
            ->first();

        if ($existing) {
            $this->addError('review', 'You have already reviewed this product.');
            return;
        }

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $this->product->id,
            'rating' => $this->rating,
            'title' => trim($this->reviewTitle) ?: null,
            'body' => trim($this->reviewBody) ?: null,
            'approved' => true,
        ]);

        $this->product->refresh();
        $this->rating = 5;
        $this->reviewTitle = '';
        $this->reviewBody = '';
        $this->resetValidation();
        $this->dispatch('review-submitted');
    }

    public function addToWishlist(): void
    {
        if (! auth()->check()) {
            $this->dispatch('wishlist-login-required');
            return;
        }

        $wishlist = Wishlist::with('items')
            ->firstOrCreate(
                ['user_id' => auth()->id(), 'name' => 'Default']
            );

        $exists = $wishlist->items->contains('product_id', $this->product->id);

        if ($exists) {
            $wishlist->items()->where('product_id', $this->product->id)->delete();
            $this->dispatch('wishlist-removed');
        } else {
            $wishlist->items()->create(['product_id' => $this->product->id]);
            $this->dispatch('wishlist-added');
        }

        $wishlistCount = $wishlist->items()->count();
        $this->dispatch('wishlist-updated', count: $wishlistCount);
    }

    public function render()
    {
        // Optimize wishlist check with eager loading
        $isInWishlist = false;
        if (auth()->check()) {
            $wishlist = Wishlist::with('items')
                ->where('user_id', auth()->id())
                ->where('name', 'Default')
                ->first();
            
            if ($wishlist) {
                $isInWishlist = $wishlist->items->contains('product_id', $this->product->id);
            }
        }

        // Get cart quantity for this product
        $cart = Cart::current();
        $cartItem = $cart->items()->where('product_id', $this->product->id)->first();
        $cartQuantity = $cartItem ? $cartItem->quantity : 0;

        // Recommended products: same category, exclude current, limit 4
        $recommendedProducts = Product::with(['category', 'images' => function ($q) {
            $q->orderBy('is_primary', 'desc')->orderBy('sort_order')->limit(1);
        }])
            ->where('status', 'active')
            ->where('id', '!=', $this->product->id)
            ->when($this->product->category_id, fn ($q) => $q->where('category_id', $this->product->category_id))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Reviews: approved only, with user, latest first
        $reviews = $this->product->approvedReviews()
            ->with('user:id,name')
            ->latest()
            ->get();

        $userReview = null;
        $canReview = false;
        if (auth()->check()) {
            $userReview = $reviews->firstWhere('user_id', auth()->id());
            $canReview = $userReview === null;
        }

        return view('livewire.product-show', [
            'product' => $this->product,
            'isInWishlist' => $isInWishlist,
            'cartQuantity' => $cartQuantity,
            'recommendedProducts' => $recommendedProducts,
            'reviews' => $reviews,
            'userReview' => $userReview,
            'canReview' => $canReview,
        ]);
    }
}
