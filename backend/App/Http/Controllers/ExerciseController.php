<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{

    public function index()
    {
        return response()->json(Exercise::all());
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
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
            'image' => $imageName,
        ]);

        return response()->json($exercise, 201);
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
