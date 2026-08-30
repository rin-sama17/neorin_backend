<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    use HttpResponses;

    /**
     * Handle an incoming request.
     *
     * Usage:
     *   ->middleware('permission:products')            -> derives products.view/create/update/delete
     *   ->middleware('permission:custom-products.manage-rules') -> exact slug (no derivation)
     *   ->middleware('permission:orders.view-own,orders.view')  -> any-of exact slugs
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->error(null, 'لطفا ابتدا وارد شوید', 401);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        $required = collect($permissions)
            ->map(fn(string $permission) => str_contains($permission, '.')
                ? $permission
                : $permission . '.' . $this->actionToOperation($request->route()->getActionMethod()));

        if ($user->hasAnyPermission(...$required)) {
            return $next($request);
        }

        return $this->error(null, 'شما اجازه انجام این عملیات را ندارید.', 403);
    }

    private function actionToOperation(string $method): string
    {
        return match ($method) {
            'index', 'show' => 'view',
            'store'          => 'create',
            'update'         => 'update',
            'destroy'        => 'delete',
            default          => $method,
        };
    }
}
