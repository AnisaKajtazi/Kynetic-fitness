<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;
use Carbon\Carbon;
use App\Models\WeeklyPlan;
use App\Models\OrderItem;
use App\Models\ExercisePlanItem;

class ExerciseProgressController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $user->UserID;
        $startOfWeek = Carbon::now()->startOfWeek();

        $activity = strtolower($user->activity_level ?? '');
        $focus = strtolower($user->focus_area ?? '');

        
        $today = Carbon::today();

        $mealCategories = OrderItem::with('meal', 'order')
            ->whereHas('order', function ($q) use ($userId, $today) {
                $q->where('user_id', $userId)
                  ->whereDate('created_at', $today)
                  ->where('status', 'completed');
            })
            ->where('consumed', 1)
            ->get()
            ->pluck('meal.category')
            ->filter()
            ->unique()
            ->toArray();

     
            
        $recommended = Exercise::query()
            ->when($focus, function ($q) use ($focus) {
                if ($focus === 'cardio') {
                    $q->where('category', 'Cardio');
                } elseif ($focus === 'upper body') {
                    $q->where('category', 'Upper Body');
                } elseif ($focus === 'lower body') {
                    $q->where('category', 'Lower Body');
                }
            })
            ->when(!empty($mealCategories), function ($q) use ($mealCategories) {
                $q->orWhereIn('category', $mealCategories);
            })
            ->when($activity === 'high', function ($q) {
                $q->whereIn('level', ['Intermediate', 'Advanced']);
            })
            ->inRandomOrder()
            ->take(5)
            ->get();


            if ($recommended->count() < 5) {
            $additional = Exercise::whereNotIn(
                'ExerciseID',
                $recommended->pluck('ExerciseID')
            )
            ->inRandomOrder()
            ->limit(5 - $recommended->count())
            ->get();

            $recommended = $recommended->merge($additional);
        }

        

        $plan = WeeklyPlan::where('user_id', $userId)
            ->where('week_start', $startOfWeek)
            ->first();

        $progress = collect();

        if ($plan) {
            $progress = ExercisePlanItem::with('exercise')
                ->where('plan_id', $plan->id)
                ->where('completed', 1)
                ->get();
        }

       

        $categoryStats = [];
        $total = 0;

        foreach ($progress as $p) {
            $cat = $p->exercise->category ?? 'Other';
            $categoryStats[$cat] = ($categoryStats[$cat] ?? 0) + ($p->reps ?? 0);
            $total += ($p->reps ?? 0);
        }

        foreach ($categoryStats as $key => $value) {
            $categoryStats[$key] = round(($value / max($total, 1)) * 100, 1);
        }

        
        
        $daysMap = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

        $days = [];

        foreach ($daysMap as $i => $dayName) {
            $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $days[$date] = 0;
        }

        foreach ($progress as $p) {
            $index = array_search($p->day_of_week, $daysMap);

            if ($index !== false) {
                $date = $startOfWeek->copy()->addDays($index)->format('Y-m-d');
                $days[$date] += ($p->reps ?? 0);
            }
        }

        $formattedDays = [];

        foreach ($days as $date => $val) {
            $formattedDays[] = [
                'day' => Carbon::parse($date)->format('D'),
                'duration' => $val
            ];
        }

        return response()->json([
            'recommended' => $recommended,
            'categoryStats' => $categoryStats,
            'byDay' => $formattedDays,
            'history' => $progress
        ]);
    }
}