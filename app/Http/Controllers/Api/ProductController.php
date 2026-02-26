<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['images', 'category'])
            ->where('status', 'active');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderByDesc('id')->paginate(
            (int) $request->query('per_page', 12)
        );

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['images', 'category']);

        return response()->json($product);
    }
}

