<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        $requireAny = false;
        $permissionList = collect($permissions)
            ->flatMap(function (string $permission) use (&$requireAny) {
                if (str_contains($permission, '|')) {
                    $requireAny = true;

                    return explode('|', $permission);
                }

                return [$permission];
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $hasPermission = $requireAny
            ? $user?->hasAnyPermission($permissionList)
            : $user?->hasPermission($permissionList);

        if (!$user || !$hasPermission) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
