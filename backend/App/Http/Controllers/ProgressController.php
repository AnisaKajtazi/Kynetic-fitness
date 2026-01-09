<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProgressController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId = $user->UserID;
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        try {
            $cartItems = OrderItem::with('meal', 'order')
                ->whereHas('order', function ($query) use ($userId, $startOfWeek, $endOfWeek) {
                    $query->where('user_id', $userId)
                          ->where('status', 'completed')
                          ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                })
                ->get();

            $formattedItems = $cartItems->map(function ($item) {
                return [
                    'cart_id' => $item->OrderItemID,
                    'item_name' => $item->meal->name ?? 'Unknown Meal',
                    'category' => $item->meal->category ?? 'Other',
                    'calories' => $item->meal->calories ?? 0,
                    'quantity' => $item->quantity,
                    'consumed' => $item->consumed ? 1 : 0,
                    'date' => $item->order->created_at->format('Y-m-d'),
                ];
            });

            $categoryCounts = [];
            $totalConsumed = 0;
            foreach ($formattedItems as $item) {
                if ($item['consumed']) {
                    $cat = $item['category'];
                    $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
                    $totalConsumed++;
                }
            }

            $categoryPercentages = [];
            foreach ($categoryCounts as $cat => $count) {
                $categoryPercentages[$cat] = round(($count / max($totalConsumed, 1)) * 100, 1);
            }

            $daysOfWeek = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
                $daysOfWeek[$date] = 0;
            }

            foreach ($formattedItems as $item) {
                if ($item['consumed']) {
                    $daysOfWeek[$item['date']] += $item['calories'] * $item['quantity'];
                }
            }

            $formattedDays = [];
            foreach ($daysOfWeek as $date => $calories) {
                $formattedDays[] = [
                    'day' => Carbon::parse($date)->format('D'),
                    'calories' => $calories
                ];
            }

            return response()->json([
                'cartItems' => $formattedItems,
                'byCategory' => $categoryPercentages,
                'byDay' => $formattedDays,
            ]);

        } catch (\Exception $e) {
            Log::error("ProgressController error: " . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function markConsumed(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer'
        ]);

        OrderItem::whereIn('OrderItemID', $request->items)
            ->update([
                'consumed' => 1,
                'consumed_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Meals marked as consumed successfully'
        ]);
    }
}
