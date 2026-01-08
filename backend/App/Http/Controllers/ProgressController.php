<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProgressController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated'
            ], 401);
        }

        $userId = $user->id;

        try {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            $cartItems = DB::table('my_cart')
                ->join('meals', 'meals.MealID', '=', 'my_cart.meal_id')
                ->where('my_cart.user_id', $userId)
                ->whereBetween('my_cart.created_at', [$startOfWeek, $endOfWeek])
                ->select(
                    'my_cart.cart_id',
                    'my_cart.meal_id',
                    'my_cart.item_name',
                    'my_cart.price',
                    'my_cart.quantity',
                    'meals.category',
                    'meals.calories',
                    DB::raw('DATE(my_cart.created_at) as date')
                )
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'totalCalories' => 0,
                    'byCategory'    => [],
                    'byDay'         => [],
                    'cartItems'     => []
                ]);
            }


            $categoryTotals = [];
            $totalCalories = 0;

            foreach ($cartItems as $item) {
                $category = $item->category ?? 'Other';
                $calories = ($item->calories ?? 0) * ($item->quantity ?? 0);

                $totalCalories += $calories;

                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = 0;
                }

                $categoryTotals[$category] += $calories;

                $item->consumed = 0;
            }


            $daysOfWeek = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
                $daysOfWeek[$date] = 0;
            }

            foreach ($cartItems as $item) {
                $date = $item->date;
                if (isset($daysOfWeek[$date])) {
                    $daysOfWeek[$date] += ($item->calories ?? 0) * ($item->quantity ?? 0);
                }
            }

            $formattedDays = [];
            foreach ($daysOfWeek as $date => $calories) {
                $formattedDays[] = [
                    'day'      => Carbon::parse($date)->format('D'),
                    'calories' => $calories
                ];
            }

            return response()->json([
                'totalCalories' => $totalCalories,
                'byCategory'    => $categoryTotals,
                'byDay'         => $formattedDays,
                'cartItems'     => $cartItems
            ]);

        } catch (\Exception $e) {
            Log::error("ProgressController error: " . $e->getMessage());
            return response()->json([
                'error'   => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
