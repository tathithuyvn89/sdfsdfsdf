<?php

namespace App\Http\Middleware;
use Spatie\Permission\Exceptions\UnauthorizedException;

use Closure;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$permission)
    {
        if (app('auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            $user = app('auth')->user();
            if ($user->hasPermission($permission)) {
                $request->merge(['user_id' => $user->id]);
                return $next($request);
            }
        }
        throw UnauthorizedException::forPermissions($permissions);
    }
}
