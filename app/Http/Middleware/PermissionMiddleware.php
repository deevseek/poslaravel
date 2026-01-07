<?php

namespace App\Http\Middleware;

use App\Support\SubscriptionFeatureGate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function __construct(private SubscriptionFeatureGate $featureGate)
    {
    }

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

        $allowedPermissions = $this->featureGate->filterPermissionsForTenant($permissionList);

        if (is_array($allowedPermissions)) {
            if ($requireAny) {
                $permissionList = array_values(array_intersect($permissionList, $allowedPermissions));

                if ($permissionList === []) {
                    abort(403, 'Unauthorized');
                }
            } elseif (array_diff($permissionList, $allowedPermissions) !== []) {
                abort(403, 'Unauthorized');
            }
        }

        $hasPermission = $requireAny
            ? $user?->hasAnyPermission($permissionList)
            : $user?->hasPermission($permissionList);

        if (!$user || !$hasPermission) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
