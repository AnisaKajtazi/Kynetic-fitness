<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffSchedule;

class StaffScheduleController extends Controller
{
    public function mySchedule()
    {
        $userId = auth()->user()->UserID;

        $schedule = StaffSchedule::with('role')
            ->where('UserID', $userId)
            ->where('isAvailable', true)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json($schedule);
    }

    public function index()
    {
        $schedule = StaffSchedule::with(['user', 'role'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json($schedule);
    }
}
