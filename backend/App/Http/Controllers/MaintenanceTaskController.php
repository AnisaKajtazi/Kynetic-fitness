<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MaintenanceTaskController extends Controller
{
    private function authorizeMaintenance(): void
    {
        $user = JWTAuth::parseToken()->authenticate();

        abort_if((int) $user->RoleID !== 3 || $user->staff_type !== 'maintenance', 403, 'Forbidden');
    }

    private function authorizeViewer(): void
    {
        $user = JWTAuth::parseToken()->authenticate();

        $isAdmin = (int) $user->RoleID === 1;
        $isMaintenance = (int) $user->RoleID === 3 && $user->staff_type === 'maintenance';

        abort_if(!$isAdmin && !$isMaintenance, 403, 'Forbidden');
    }

    public function index(): JsonResponse
    {
        $this->authorizeViewer();

        $tasks = MaintenanceTask::query()
            ->orderByRaw("FIELD(status, 'Pending', 'In Progress', 'Completed')")
            ->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low')")
            ->orderBy('due_date')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tasks);
    }

    public function storeIssue(Request $request): JsonResponse
    {
        $this->authorizeMaintenance();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $issue = MaintenanceTask::create([
            ...$data,
            'type' => 'Equipment Issue',
        ]);

        return response()->json($issue, 201);
    }

    public function updateIssue(Request $request, MaintenanceTask $maintenanceTask): JsonResponse
    {
        $this->authorizeMaintenance();

        abort_if($maintenanceTask->type !== 'Equipment Issue', 422, 'Only equipment issues can be edited here.');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $maintenanceTask->update($data);

        return response()->json($maintenanceTask->fresh());
    }

    public function destroyIssue(MaintenanceTask $maintenanceTask): JsonResponse
    {
        $this->authorizeMaintenance();

        abort_if($maintenanceTask->type !== 'Equipment Issue', 422, 'Only equipment issues can be deleted here.');

        $maintenanceTask->delete();

        return response()->json(['message' => 'Equipment issue deleted.']);
    }

    public function updateCompletion(Request $request, MaintenanceTask $maintenanceTask): JsonResponse
    {
        $this->authorizeMaintenance();

        abort_if($maintenanceTask->type !== 'Task', 422, 'Only maintenance tasks can be completed here.');

        $data = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $maintenanceTask->update([
            'status' => $data['completed'] ? 'Completed' : 'Pending',
        ]);

        return response()->json($maintenanceTask->fresh());
    }
}
