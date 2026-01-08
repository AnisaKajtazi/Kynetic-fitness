<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyCart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Meal;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function createStripeCheckout()
    {
        $userId = auth()->id();
        $cartItems = MyCart::where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $lineItems = [];

        foreach ($cartItems as $item) {
            if (!$item->price || $item->price <= 0) {
                return response()->json(['message' => "Invalid price for meal ID {$item->meal_id}"], 400);
            }
            if (!$item->quantity || $item->quantity <= 0) {
                return response()->json(['message' => "Invalid quantity for meal ID {$item->meal_id}"], 400);
            }

            $meal = Meal::find($item->meal_id);
            $mealName = $meal ? $meal->name : 'Meal ID: ' . $item->meal_id;

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $mealName,
                    ],
                    'unit_amount' => (int) ($item->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'client_reference_id' => $userId,
                'success_url' => url('/api/checkout/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url'  => url('/api/checkout/cancel'),
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Stripe Checkout creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function success(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            if (!$request->session_id) {
                return redirect('http://localhost:5173/dashboard');
            }

            $session = Session::retrieve($request->session_id);
            if (!$session) {
                return redirect('http://localhost:5173/dashboard');
            }

            $userId = $session->client_reference_id;
            $cartItems = MyCart::where('user_id', $userId)->get();

            if ($cartItems->isEmpty()) {
                return redirect('http://localhost:5173/dashboard');
            }

            DB::beginTransaction();

            $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity);

            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
                'status' => 'completed',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->OrderID,
                    'meal_id' => $item->meal_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }

            MyCart::where('user_id', $userId)->delete();

            DB::commit();

            return redirect('http://localhost:5173/orders/success?order_id=' . $order->OrderID);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stripe success error: '.$e->getMessage());
            return redirect('http://localhost:5173/dashboard?activeSection=orderserror');
        }
    }

    public function cancel()
    {
        return redirect('http://localhost:5173/dashboard?activeSection=orderscancel');
    }
}
