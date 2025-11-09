<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFavorite;
use App\Models\Exercise;
use Illuminate\Support\Facades\Auth;

class UserFavoriteController extends Controller
{

   public function index()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([]);
    }

    return UserFavorite::where('user_id', $user->UserID)
        ->join('exercises', 'user_favorites.exercise_id', '=', 'exercises.ExerciseID')
        ->select('exercises.*')
        ->get();
}

    
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'exercise_id' => 'required|exists:exercises,ExerciseID',
        ]);

        $favorite = UserFavorite::firstOrCreate([
            'user_id' => $user->UserID,
            'exercise_id' => $request->exercise_id,
        ]);

    
        $exercise = Exercise::find($request->exercise_id);

        return response()->json([
            'ExerciseID' => $exercise->ExerciseID,
            'name' => $exercise->name,
            'category' => $exercise->category,
            'level' => $exercise->level,
            'description' => $exercise->description,
            'image' => $exercise->image,
        ]);
    }

    
    public function destroy($exercise_id)
    {
        $user = Auth::user();

        $deleted = UserFavorite::where('user_id', $user->UserID)
            ->where('exercise_id', $exercise_id)
            ->delete();

        return response()->json(['deleted' => $deleted]);
    }
}
