<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class OrderInvoiceController extends Controller
{
    public function __invoke(string $orderNumber): Response
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items', 'shippingAddress', 'billingAddress'])
            ->firstOrFail();

        $pdf = Pdf::loadView('invoices.order', compact('order'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
