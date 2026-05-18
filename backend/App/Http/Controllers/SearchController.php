<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 1) {
            return response()->json(['error' => 'Query too short'], 400);
        }

        $results = [
            'exercises' => [],
            'meals' => [],
            'trainers' => [],
            'type' => null,
            'item' => null,
        ];

        // Search exercises
        $exercises = Exercise::where('exercise_name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();
        $results['exercises'] = $exercises;

        // Search meals
        $meals = Meal::where('meal_name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();
        $results['meals'] = $meals;

        // Search trainers
        $trainers = User::where('RoleID', 3) // 3 = trainer role
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('surname', 'LIKE', "%{$query}%")
                    ->orWhere('focus_area', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get(['UserID', 'name', 'surname', 'photo', 'focus_area', 'description']);
        
        $results['trainers'] = $trainers->map(function ($trainer) {
            return [
                'UserID' => $trainer->UserID,
                'fullName' => trim("{$trainer->name} {$trainer->surname}"),
                'photo' => $trainer->photo,
                'focus_area' => $trainer->focus_area,
                'description' => $trainer->description,
            ];
        });

        // Determine primary type based on closest match
        if (count($exercises) > 0) {
            $results['type'] = 'exercise';
            $results['item'] = $exercises[0];
        } elseif (count($meals) > 0) {
            $results['type'] = 'meal';
            $results['item'] = $meals[0];
        } elseif (count($trainers) > 0) {
            $results['type'] = 'trainer';
            $results['item'] = $results['trainers'][0];
        }

        return response()->json($results);
    }
}
