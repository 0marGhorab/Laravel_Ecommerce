<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected function getWishlist(): Wishlist
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return Wishlist::with('items.product.images')
            ->firstOrCreate(
                ['user_id' => $user->id, 'name' => 'Default']
            );
    }

    public function index(): JsonResponse
    {
        $wishlist = $this->getWishlist()->load('items.product.images');

        return response()->json([
            'id' => $wishlist->id,
            'items' => $wishlist->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'image_url' => $item->product?->images->first()->url ?? null,
                ];
            }),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $wishlist = $this->getWishlist();
        $product = Product::findOrFail($data['product_id']);

        if (! $wishlist->items()->where('product_id', $product->id)->exists()) {
            $wishlist->items()->create(['product_id' => $product->id]);
        }

        return $this->index();
    }

    public function destroy(int $productId): JsonResponse
    {
        $wishlist = $this->getWishlist();
        $wishlist->items()->where('product_id', $productId)->delete();

        return $this->index();
    }
}

