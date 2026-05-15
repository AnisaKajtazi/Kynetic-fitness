<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeeklyPlan;
use App\Models\ExercisePlanItem;
use Carbon\Carbon;

class ExercisesOfTheWeekController extends Controller
{
   
    public function getWeek(Request $request)
{
    $user = $request->user();

    $weekStart = Carbon::now()->startOfWeek();

    $plan = WeeklyPlan::firstOrCreate([
        'user_id' => $user->UserID,
        'week_start' => $weekStart
    ]);

    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    $items = ExercisePlanItem::with('exercise')
        ->where('plan_id', $plan->id)
        ->get()
        ->groupBy('day_of_week');

    $result = [];

    foreach ($days as $day) {
        $result[$day] = $items[$day] ?? [];
    }

    return response()->json($result);
}

    

    public function addExercise(Request $request)
    {
        $request->validate([
            'exercise_id' => 'required|integer',
            'day_of_week' => 'required|string',
            'reps' => 'nullable|integer|min:1'
        ]);

        $user = $request->user();
        $weekStart = Carbon::now()->startOfWeek();

        $plan = WeeklyPlan::firstOrCreate([
            'user_id' => $user->UserID,
            'week_start' => $weekStart
        ]);


        $existing = ExercisePlanItem::where('plan_id', $plan->id)
            ->where('day_of_week', $request->day_of_week)
            ->where('exercise_id', $request->exercise_id)
            ->first();

        if ($existing) {
            $existing->reps = $request->reps ?? $existing->reps;
            $existing->save();

            return response()->json([
                'item' => $existing,
                'already_exists' => true
            ]);
        }


        $count = ExercisePlanItem::where('plan_id', $plan->id)
            ->where('day_of_week', $request->day_of_week)
            ->count();

        if ($count >= 10) {
            return response()->json([
                'error' => 'Max 10 exercises per day'
            ], 400);
        }


        $item = ExercisePlanItem::create([
            'plan_id' => $plan->id,
            'exercise_id' => $request->exercise_id,
            'day_of_week' => $request->day_of_week,
            'reps' => $request->reps ?? 3
        ]);

       return response()->json([
        'item' => $item,
        'already_exists' => false
    ]);
            }

    

    public function toggleComplete($id)
    {
        $item = ExercisePlanItem::findOrFail($id);

        $item->completed = !$item->completed;
        $item->completed_at = $item->completed ? now() : null;
        $item->save();

        return response()->json($item);
    }

   

    public function completeAll(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|string'
        ]);

        $user = $request->user();
        $weekStart = Carbon::now()->startOfWeek();

        $plan = WeeklyPlan::where('user_id', $user->UserID)
            ->where('week_start', $weekStart)
            ->first();

        if (!$plan) return response()->json([]);

        ExercisePlanItem::where('plan_id', $plan->id)
            ->where('day_of_week', $request->day_of_week)
            ->update([
                'completed' => 1,
                'completed_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    
    
    public function delete($id)
    {
        $item = ExercisePlanItem::findOrFail($id);
        $item->delete();

        return response()->json(['success' => true]);
    }
    public function updateReps(Request $request, $id)
{
    $request->validate([
        'reps' => 'required|integer|min:1'
    ]);

    $item = ExercisePlanItem::findOrFail($id);
    $item->reps = $request->reps;
    $item->save();

    return response()->json($item);
}
}
