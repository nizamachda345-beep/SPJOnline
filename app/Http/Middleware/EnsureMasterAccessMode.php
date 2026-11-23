<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AccessModeController;
use Closure;
use Illuminate\Http\Request;

class EnsureMasterAccessMode
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('access-mode.*')) {
            return $next($request);
        }

        $user = $request->user();
        if ($user?->isActualSuperAdmin() && !array_key_exists((string) $user->accessMode(), AccessModeController::MODES)) {
            $request->session()->put('master_access_mode', 'super_admin');
        }

        return $next($request);
    }
}
