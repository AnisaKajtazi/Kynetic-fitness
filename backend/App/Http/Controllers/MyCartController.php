<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyCart;
use Illuminate\Support\Facades\Auth;
use App\Models\Meal;

class MyCartController extends Controller
{
    public function index()
    {
        $cart = MyCart::with('meal')->where('user_id', Auth::id())->get();
    return response()->json($cart);
    }


    public function store(Request $request)
    {
        \Log::info('Cart POST request', $request->all());

        $request->validate([
            'meal_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
            'item_name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $userId = Auth::id();
        $mealId = intval($request->meal_id);
        $quantity = $request->quantity ?? 1;

        $meal = Meal::find($mealId);
        if (!$meal) {
            return response()->json(['message' => 'Meal not found'], 404);
        }

        $cartItem = MyCart::firstOrNew([
            'user_id' => $userId,
            'meal_id' => $mealId,
        ]);

        if ($cartItem->exists) {
            $cartItem->quantity += $quantity;
        } else {
            $cartItem->item_name = $request->item_name;
            $cartItem->price = $request->price;
            $cartItem->quantity = $quantity;
        }

        $cartItem->save();
        return response()->json($cartItem);
    }


    public function updateQuantity(Request $request, $meal_id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $cartItem = MyCart::where('user_id', $userId)
                          ->where('meal_id', $meal_id)
                          ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json($cartItem);
    }

    public function destroy($meal_id)
    {
        $userId = Auth::id();
        $item = MyCart::where('user_id', $userId)
                      ->where('meal_id', $meal_id)
                      ->first();

        if ($item) {
            $item->delete();
            return response()->json(['message' => 'Removed from cart']);
        }

        return response()->json(['message' => 'Item not found'], 404);
    }
}
