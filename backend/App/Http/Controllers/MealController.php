<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function index(Request $request)
    {
        $query = Meal::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $meals = $query->paginate($request->per_page ?? 10);
        return response()->json($meals);
    }

 
    public function allMeals()
    {
        $meals = Meal::all()->groupBy('category');
        return response()->json($meals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'calories' => 'nullable|integer',

            'fitness_goal' => 'nullable|in:lose weight,gain muscle,stay fit',
            'activity_level' => 'nullable|in:low,medium,high',
            'focus_area' => 'nullable|in:upper body,lower body,cardio',
            'training_days' => 'nullable|integer|min:0|max:7',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads'), $imageName);
            $validated['image'] = $imageName;
        }

        $meal = Meal::create($validated);

        return response()->json($meal, 201);
    }


    public function show($id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return response()->json(['message' => 'Meal not found'], 404);
        }

        return response()->json($meal);
    }


    public function update(Request $request, $id)
    {
        $meal = Meal::find($id);
        if (!$meal) {
            return response()->json(['message' => 'Meal not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'calories' => 'nullable|integer',

            'fitness_goal' => 'nullable|in:lose weight,gain muscle,stay fit',
            'activity_level' => 'nullable|in:low,medium,high',
            'focus_area' => 'nullable|in:upper body,lower body,cardio',
            'training_days' => 'nullable|integer|min:0|max:7',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($meal->image && file_exists(public_path('uploads/' . $meal->image))) {
                unlink(public_path('uploads/' . $meal->image));
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads'), $imageName);
            $validated['image'] = $imageName;
        }

        $meal->update($validated);

        return response()->json($meal);
    }

   
    public function destroy($id)
    {
        $meal = Meal::find($id);
        if (!$meal) {
            return response()->json(['message' => 'Meal not found'], 404);
        }

        if ($meal->image && file_exists(public_path('uploads/' . $meal->image))) {
            unlink(public_path('uploads/' . $meal->image));
        }

        $meal->delete();

        return response()->json(['message' => 'Meal deleted successfully']);
    }
}
