<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Services\SystemSettingsService;
use Carbon\CarbonImmutable;

class CheckDepartmentUpload
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user->hasRole(['super_admin', 'kabid_perbendaharaan', 'kasie_bud'])) {
            return $next($request);
        }

        if (!$user->department_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda tidak terikat dengan SKPD manapun.');
        }

        $department = Department::find($user->department_id);
        $settings = app(SystemSettingsService::class);
        $evaluatedAt = CarbonImmutable::now($settings->timezone());
        $availability = $department
            ? $settings->submissionAvailability($department, $evaluatedAt)
            : ['allowed' => false, 'message' => 'Akun Anda tidak terikat dengan SKPD manapun.'];

        if (!$availability['allowed']) {
            return redirect()->route('dashboard')
                ->with('error', $availability['message']);
        }

        // Reusable only within this request; controller checks department,
        // manual access, and policy-timezone minute before accepting the snapshot.
        $request->attributes->set('department_submission_access', [
            'department_id' => (int) $department->getKey(),
            'manual_access' => $department->akses_pengajuan_manual,
            'evaluated_at' => $evaluatedAt,
            'decision' => $availability,
        ]);

        return $next($request);
    }
}
