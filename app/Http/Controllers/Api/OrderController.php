<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product.images'])
            ->orderByDesc('created_at')
            ->paginate((int) $request->query('per_page', 10));

        return response()->json($orders);
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items.product.images', 'shippingAddress', 'billingAddress'])
            ->firstOrFail();

        return response()->json($order);
    }
}

