<?php

namespace Database\Seeders;

use App\Models\User\Permission;
use App\Models\User\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'سوپر ادمین',
                'slug' => 'super-admin',
                'permissions' => [], // bypass handled in User::hasPermission() / CheckPermission
            ],
            [
                'name' => 'ادمین',
                'slug' => 'admin',
                'permissions' => [
                    'products.view',
                    'products.create',
                    'products.update',
                    'products.delete',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'categories.delete',
                    'category-attributes.view',
                    'category-attributes.create',
                    'category-attributes.update',
                    'category-attributes.delete',
                    'category-values.view',
                    'category-values.create',
                    'category-values.update',
                    'category-values.delete',
                    'fabrics.view',
                    'fabrics.create',
                    'fabrics.update',
                    'fabrics.delete',
                    'sizes.view',
                    'sizes.create',
                    'sizes.update',
                    'sizes.delete',
                    'colors.view',
                    'colors.create',
                    'colors.update',
                    'colors.delete',
                    'discounts.view',
                    'discounts.create',
                    'discounts.update',
                    'discounts.delete',
                    'states.view',
                    'states.create',
                    'states.update',
                    'states.delete',
                    'galleries.view',
                    'galleries.create',
                    'galleries.update',
                    'galleries.delete',
                    'pages.view',
                    'pages.create',
                    'pages.update',
                    'pages.delete',
                    'sliders.view',
                    'sliders.create',
                    'sliders.update',
                    'sliders.delete',
                    'settings.manage',
                    'custom-products.view',
                    'custom-products.create',
                    'custom-products.update',
                    'custom-products.delete',
                    'custom-products.manage-items',
                    'custom-products.manage-rules',
                    'calculation-profiles.view',
                    'calculation-profiles.create',
                    'calculation-profiles.update',
                    'calculation-profiles.delete',
                    'formulas.view',
                    'formulas.create',
                    'formulas.update',
                    'formulas.delete',
                    'orders.view',
                    'orders.update',
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.delete',
                    'roles.view',
                    'roles.create',
                    'roles.update',
                    'roles.delete',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'مدیر محصولات',
                'slug' => 'product-manager',
                'permissions' => [
                    'products.view',
                    'products.create',
                    'products.update',
                    'products.delete',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'categories.delete',
                    'category-attributes.view',
                    'category-attributes.create',
                    'category-attributes.update',
                    'category-attributes.delete',
                    'category-values.view',
                    'category-values.create',
                    'category-values.update',
                    'category-values.delete',
                    'fabrics.view',
                    'fabrics.create',
                    'fabrics.update',
                    'fabrics.delete',
                    'sizes.view',
                    'sizes.create',
                    'sizes.update',
                    'sizes.delete',
                    'colors.view',
                    'colors.create',
                    'colors.update',
                    'colors.delete',
                    'discounts.view',
                    'discounts.create',
                    'discounts.update',
                    'discounts.delete',
                    'states.view',
                    'states.create',
                    'states.update',
                    'states.delete',
                    'galleries.view',
                    'galleries.create',
                    'galleries.update',
                    'galleries.delete',
                    'custom-products.view',
                    'custom-products.create',
                    'custom-products.update',
                    'custom-products.delete',
                    'custom-products.manage-items',
                    'custom-products.manage-rules',
                    'calculation-profiles.view',
                    'calculation-profiles.create',
                    'calculation-profiles.update',
                    'calculation-profiles.delete',
                    'formulas.view',
                    'formulas.create',
                    'formulas.update',
                    'formulas.delete',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'مدیر سفارش‌ها',
                'slug' => 'order-manager',
                'permissions' => [
                    'orders.view',
                    'orders.update',
                    'cart.view',
                    'users.view',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'مشتری',
                'slug' => 'user',
                'permissions' => [],
            ],
        ];

        foreach ($roles as $role) {
            $roleModel = Role::updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );

            $permissionIds = Permission::whereIn('slug', $role['permissions'])
                ->pluck('id');

            $roleModel->permissions()->sync($permissionIds);
        }
    }
}
