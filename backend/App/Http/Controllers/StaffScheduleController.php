<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffSchedule;
use App\Models\User;

class StaffScheduleController extends Controller
{
    public function mySchedule()
    {
        try {
            $userId = auth()->user()->UserID;

            $schedule = StaffSchedule::with('role')
                ->where('UserID', $userId)
                ->where('isAvailable', true)
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();

            return response()->json($schedule);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            $schedule = StaffSchedule::with(['user', 'role'])
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();

            return response()->json($schedule);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showStaffSchedule($userId)
    {
        try {
            $schedule = StaffSchedule::with(['role'])
                ->where('UserID', $userId)
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();

            return response()->json($schedule ?? []);
        } catch (\Exception $e) {
            \Log::error("showStaffSchedule error: ".$e->getMessage());
            return response()->json([]);
        }
    }

public function setWeeklySchedule(Request $request, $userId)
{
    try {
        $data = $request->input('schedule'); 
        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid schedule data'], 400);
        }

        foreach ($data as $daySchedule) {
            StaffSchedule::updateOrCreate(
                [
                    'UserID' => $userId,
                    'day' => $daySchedule['day'],
                ],
                [
                    'start_time'   => $daySchedule['start_time'] ?? null,
                    'end_time'     => $daySchedule['end_time'] ?? null,
                    'isAvailable'  => $daySchedule['isAvailable'] ?? false,
                    'RoleID'       => $daySchedule['RoleID'] ?? 3
                ]
            );
        }

        return response()->json([
            'message' => 'Weekly schedule updated successfully.'
        ]);
    } catch (\Exception $e) {
        \Log::error("setWeeklySchedule error: ".$e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function staffList()
    {
        try {
            $staff = User::where('RoleID', 3)
                ->get(['UserID', 'name', 'surname']);

            $staff = $staff->map(function ($item) {
                return [
                    'UserID' => $item->UserID,
                    'first_name' => $item->name,
                    'last_name' => $item->surname
                ];
            });

            return response()->json($staff);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error in staffList',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
