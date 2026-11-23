<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\SystemSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class UserActivityController extends Controller
{
    public function index(): View
    {
        return view('admin.activity.index');
    }

    public function feed(Request $request, SystemSettingsService $settings): JsonResponse
    {
        $cutoff = now()->subSeconds(90);
        $selectedUserId = $request->query('user_id');

        abort_if($selectedUserId !== null && !ctype_digit((string) $selectedUserId), 422);

        $onlineUsers = User::query()
            ->with(['department', 'roles'])
            ->where('last_seen_at', '>=', $cutoff)
            ->orderByDesc('last_seen_at')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->roles->pluck('name')->map(fn ($role) => str_replace('_', ' ', $role))->join(', ') ?: 'Pengguna',
                'department' => $user->department?->nama_dinas,
                'last_seen_at' => $user->last_seen_at?->toIso8601String(),
            ]);

        $activities = UserActivityLog::query()
            ->when($selectedUserId !== null, fn ($query) => $query->where('user_id', (int) $selectedUserId))
            ->latest('occurred_at')
            ->limit(50)
            ->get(['id', 'actor_name', 'role_name', 'department_name', 'activity', 'http_method', 'occurred_at'])
            ->map(fn (UserActivityLog $activity) => [
                'actor' => $activity->actor_name,
                'role' => $activity->role_name,
                'department' => $activity->department_name,
                'activity' => $activity->activity,
                'method' => $activity->http_method,
                'occurred_at' => $activity->occurred_at?->toIso8601String(),
            ]);

        return response()->json([
            'online' => $onlineUsers,
            'activities' => $activities,
            'selected_user_id' => $selectedUserId !== null ? (int) $selectedUserId : null,
            'updated_at' => Carbon::now()->toIso8601String(),
            'timezone' => $settings->timezone(),
        ]);
    }
}
