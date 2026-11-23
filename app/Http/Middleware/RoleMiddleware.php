<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles, ?string $guard = null)
    {
        $user = auth($guard)->user();
        if (!$user) {
            throw UnauthorizedException::notLoggedIn();
        }

        $allowedRoles = explode('|', $roles);
        $allowed = $user->isActualSuperAdmin()
            ? in_array($request->session()->get('master_access_mode'), $allowedRoles, true)
            : $user->hasAnyRole($allowedRoles);

        if (!$allowed) {
            throw UnauthorizedException::forRoles($allowedRoles);
        }

        return $next($request);
    }
}
