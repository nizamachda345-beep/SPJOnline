<?php

namespace App\Http\Middleware;

use App\Services\SystemSettingsService;
use Closure;
use Illuminate\Http\Request;

class ApplyMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('access-mode.*')) {
            return $next($request);
        }

        if ($request->user()?->isActualSuperAdmin()) {
            return $next($request);
        }

        $settings = app(SystemSettingsService::class);
        if ($settings->hasReadFailure() || $settings->getTyped('system.maintenance_mode') === true) {
            abort(503, 'Sistem sedang dalam pemeliharaan. Silakan coba kembali nanti.');
        }

        return $next($request);
    }
}
