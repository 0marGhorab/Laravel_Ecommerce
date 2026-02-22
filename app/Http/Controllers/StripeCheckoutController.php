<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session as StripeSession;
use Stripe\StripeClient;

class StripeCheckoutController extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId || !auth()->check()) {
            return redirect()->route('checkout.index')->with('error', 'Invalid session.');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        try {
            /** @var StripeSession $session */
            $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['payment_intent']]);
        } catch (\Throwable $e) {
            \Log::warning('Stripe session retrieve failed: ' . $e->getMessage());
            return redirect()->route('checkout.index')->with('error', 'Could not verify payment.');
        }

        if ($session->payment_status !== 'paid') {
            return redirect()->route('checkout.index')->with('error', 'Payment was not completed.');
        }

        $orderId = $session->client_reference_id ?? null;
        if (!$orderId) {
            return redirect()->route('checkout.index')->with('error', 'Order reference missing.');
        }

        $order = Order::where('id', $orderId)->where('user_id', auth()->id())->first();
        if (!$order) {
            return redirect()->route('checkout.index')->with('error', 'Order not found.');
        }

        $order->update(['payment_status' => 'paid']);

        $cart = Cart::where('user_id', auth()->id())->where('status', 'active')->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->update(['status' => 'converted']);
        }

        $order->load('user');
        if ($order->user && $order->user->email) {
            Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
        }

        session()->flash('order_placed_success', true);

        return redirect()->route('orders.show', $order->order_number)->with('success', 'Payment successful. Order confirmed.');
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('checkout.index')->with('info', 'Payment was cancelled. You can try again when ready.');
    }
}
