<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    public function index()
    {
        $meals = Meal::all()->groupBy('category');
        return response()->json($meals);
    }


    public function userMeals()
    {
        $user = Auth::user();


        $allMeals = Meal::all()->groupBy('category');


        $personalisedMeals = Meal::query()
            ->when($user->fitness_goal, fn($q) => $q->where('fitness_goal', $user->fitness_goal))
            ->when($user->activity_level, fn($q) => $q->where('activity_level', $user->activity_level))
            ->when($user->training_days, fn($q) => $q->where('training_days', '<=', $user->training_days))
            ->when($user->focus_area, fn($q) => $q->where('focus_area', $user->focus_area))
            ->get()
            ->groupBy('category');


        $favorites = $user->favorites()->pluck('meal_id')->toArray();

        return response()->json([
            'all_meals' => $allMeals,
            'personalised_meals' => $personalisedMeals,
            'favorites' => $favorites
        ]);
    }


    public function addFavorite(Request $request)
    {
        $request->validate([
            'meal_id' => 'required|integer|exists:meals,MealID'
        ]);

        $user = Auth::user();
        $user->favorites()->syncWithoutDetaching([$request->meal_id]);

        return response()->json(['message' => 'Added to favorites']);
    }

 
    public function personaliseMeal(Request $request)
    {
        $request->validate([
            'meal_id' => 'required|integer|exists:meals,MealID'
        ]);

        $user = Auth::user();
        $user->personalisedMeals()->syncWithoutDetaching([$request->meal_id]);

        return response()->json(['message' => 'Meal added to personalised list']);
    }
}
