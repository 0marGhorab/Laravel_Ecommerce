<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(): JsonResponse
    {
        $cart = Cart::current()->load('items.product.images');

        return response()->json([
            'id' => $cart->id,
            'items' => $cart->items->map(function (CartItem $item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'sku' => $item->product?->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'image_url' => $item->product?->images->first()->url ?? null,
                ];
            }),
            'subtotal' => (float) $cart->items->sum('total_price'),
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $quantity = $data['quantity'] ?? 1;

        $cart = Cart::current();
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $newQty = $item->exists ? $item->quantity + $quantity : $quantity;

        $item->quantity = $newQty;
        $item->unit_price = $product->price;
        $item->total_price = $item->quantity * $item->unit_price;
        $item->save();

        Cart::clearCache();

        return $this->show();
    }

    public function updateItem(Request $request, CartItem $item): JsonResponse
    {
        $cart = Cart::current();
        if ($item->cart_id !== $cart->id) {
            abort(403);
        }

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        if ($data['quantity'] === 0) {
            $item->delete();
        } else {
            $item->quantity = $data['quantity'];
            $item->total_price = $item->quantity * $item->unit_price;
            $item->save();
        }

        Cart::clearCache();

        return $this->show();
    }

    public function removeItem(CartItem $item): JsonResponse
    {
        $cart = Cart::current();
        if ($item->cart_id !== $cart->id) {
            abort(403);
        }

        $item->delete();
        Cart::clearCache();

        return $this->show();
    }

    public function clear(): JsonResponse
    {
        $cart = Cart::current();
        $cart->items()->delete();
        Cart::clearCache();

        return $this->show();
    }
}

