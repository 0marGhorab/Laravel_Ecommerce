<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $usersCount = User::count();

        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        $totalRevenue = Order::where('payment_status', 'paid')->sum('grand_total');

        return view('admin.dashboard', [
            'ordersCount' => $ordersCount,
            'productsCount' => $productsCount,
            'usersCount' => $usersCount,
            'recentOrders' => $recentOrders,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}
