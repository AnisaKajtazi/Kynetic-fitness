<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $query = Exercise::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%");
        }

        $exercises = $query->paginate($request->per_page ?? 10);

        return response()->json($exercises);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads'), $imageName);
        }

        $exercise = Exercise::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'category' => $request->category,
            'level' => $request->level,
            'image' => $imageName,
        ]);

        return response()->json($exercise, 201);
    }

    
    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($exercise->image && file_exists(public_path('uploads/' . $exercise->image))) {
                unlink(public_path('uploads/' . $exercise->image));
            }
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads'), $imageName);
            $exercise->image = $imageName;
        }

        $exercise->name = $request->name;
        $exercise->description = $request->description;
        $exercise->duration = $request->duration;
        $exercise->category = $request->category;
        $exercise->level = $request->level;

        $exercise->save();

        return response()->json($exercise);
    }


    public function show($id)
    {
        return response()->json(Exercise::findOrFail($id));
    }


    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);

        if ($exercise->image && file_exists(public_path('uploads/' . $exercise->image))) {
            unlink(public_path('uploads/' . $exercise->image));
        }

        $exercise->delete();

        return response()->json(['message' => 'Exercise deleted successfully']);
    }
}
