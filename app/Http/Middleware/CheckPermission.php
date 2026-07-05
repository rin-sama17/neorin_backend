<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        $routeName = $request->route()->getName();

        $permission = $this->routeToPermission($routeName);

        if (!$permission) {
            return response()->json([
                'message' => 'Permission mapping not found.'
            ], 500);
        }

        if (!Auth::user()->hasPermission($permission)) {
            return response()->json([
                'message' => 'شما دسترسی لازم برای این عملیات را ندارید'
            ], 403);
        }

        return $next($request);
    }

    private function routeToPermission(string $routeName): ?string
    {
        $parts = explode('.', $routeName);

        // admin.product.products.index

        if (count($parts) < 4) {
            return null;
        }

        $resource = $this->resourceMap()[$parts[2]] ?? null;

        if (!$resource) {
            return null;
        }

        $action = match ($parts[3]) {
            'index', 'show' => 'view',
            'store' => 'create',
            'update' => 'update',
            'destroy' => 'delete',
            default => null,
        };

        if (!$action) {
            return null;
        }

        return "{$resource}.{$action}";
    }

    private function resourceMap(): array
    {
        return [
            'products' => 'product',
            'category' => 'category',
            'colors' => 'color',
            'sizes' => 'size',
            'discounts' => 'discount',
            'fabrics' => 'fabric',
            'gallery' => 'gallery',
            'page' => 'page',
            'slider' => 'slider',
            'user' => 'user',
            'setting' => 'setting',
            'category-attribute' => 'category-attribute',
            'category-value' => 'category-value',
            'state' => 'state',
        ];
    }
}
