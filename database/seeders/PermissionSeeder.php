<?php

namespace Database\Seeders;

use App\Models\User\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Products
            ['name' => 'مشاهده محصولات', 'slug' => 'products.view'],
            ['name' => 'ایجاد محصول', 'slug' => 'products.create'],
            ['name' => 'ویرایش محصول', 'slug' => 'products.update'],
            ['name' => 'حذف محصول', 'slug' => 'products.delete'],

            // Categories
            ['name' => 'مشاهده دسته‌بندی‌ها', 'slug' => 'categories.view'],
            ['name' => 'ایجاد دسته‌بندی', 'slug' => 'categories.create'],
            ['name' => 'ویرایش دسته‌بندی', 'slug' => 'categories.update'],
            ['name' => 'حذف دسته‌بندی', 'slug' => 'categories.delete'],

            // Category attributes
            ['name' => 'مشاهده ویژگی‌های دسته‌بندی', 'slug' => 'category-attributes.view'],
            ['name' => 'ایجاد ویژگی دسته‌بندی', 'slug' => 'category-attributes.create'],
            ['name' => 'ویرایش ویژگی دسته‌بندی', 'slug' => 'category-attributes.update'],
            ['name' => 'حذف ویژگی دسته‌بندی', 'slug' => 'category-attributes.delete'],

            // Category values
            ['name' => 'مشاهده مقادیر دسته‌بندی', 'slug' => 'category-values.view'],
            ['name' => 'ایجاد مقدار دسته‌بندی', 'slug' => 'category-values.create'],
            ['name' => 'ویرایش مقدار دسته‌بندی', 'slug' => 'category-values.update'],
            ['name' => 'حذف مقدار دسته‌بندی', 'slug' => 'category-values.delete'],

            // Fabrics
            ['name' => 'مشاهده پارچه‌ها', 'slug' => 'fabrics.view'],
            ['name' => 'ایجاد پارچه', 'slug' => 'fabrics.create'],
            ['name' => 'ویرایش پارچه', 'slug' => 'fabrics.update'],
            ['name' => 'حذف پارچه', 'slug' => 'fabrics.delete'],

            // Sizes
            ['name' => 'مشاهده سایزها', 'slug' => 'sizes.view'],
            ['name' => 'ایجاد سایز', 'slug' => 'sizes.create'],
            ['name' => 'ویرایش سایز', 'slug' => 'sizes.update'],
            ['name' => 'حذف سایز', 'slug' => 'sizes.delete'],

            // Colors
            ['name' => 'مشاهده رنگ‌ها', 'slug' => 'colors.view'],
            ['name' => 'ایجاد رنگ', 'slug' => 'colors.create'],
            ['name' => 'ویرایش رنگ', 'slug' => 'colors.update'],
            ['name' => 'حذف رنگ', 'slug' => 'colors.delete'],

            // Discounts
            ['name' => 'مشاهده تخفیف‌ها', 'slug' => 'discounts.view'],
            ['name' => 'ایجاد تخفیف', 'slug' => 'discounts.create'],
            ['name' => 'ویرایش تخفیف', 'slug' => 'discounts.update'],
            ['name' => 'حذف تخفیف', 'slug' => 'discounts.delete'],

            // States
            ['name' => 'مشاهده استان‌ها', 'slug' => 'states.view'],
            ['name' => 'ایجاد استان', 'slug' => 'states.create'],
            ['name' => 'ویرایش استان', 'slug' => 'states.update'],
            ['name' => 'حذف استان', 'slug' => 'states.delete'],

            // Galleries
            ['name' => 'مشاهده گالری‌ها', 'slug' => 'galleries.view'],
            ['name' => 'ایجاد تصویر گالری', 'slug' => 'galleries.create'],
            ['name' => 'ویرایش تصویر گالری', 'slug' => 'galleries.update'],
            ['name' => 'حذف تصویر گالری', 'slug' => 'galleries.delete'],

            // Pages
            ['name' => 'مشاهده صفحات', 'slug' => 'pages.view'],
            ['name' => 'ایجاد صفحه', 'slug' => 'pages.create'],
            ['name' => 'ویرایش صفحه', 'slug' => 'pages.update'],
            ['name' => 'حذف صفحه', 'slug' => 'pages.delete'],

            // Sliders
            ['name' => 'مشاهده اسلایدرها', 'slug' => 'sliders.view'],
            ['name' => 'ایجاد اسلایدر', 'slug' => 'sliders.create'],
            ['name' => 'ویرایش اسلایدر', 'slug' => 'sliders.update'],
            ['name' => 'حذف اسلایدر', 'slug' => 'sliders.delete'],

            // Settings
            ['name' => 'مدیریت تنظیمات', 'slug' => 'settings.manage'],

            // Custom products
            ['name' => 'مشاهده محصولات سفارشی', 'slug' => 'custom-products.view'],
            ['name' => 'ایجاد محصول سفارشی', 'slug' => 'custom-products.create'],
            ['name' => 'ویرایش محصول سفارشی', 'slug' => 'custom-products.update'],
            ['name' => 'حذف محصول سفارشی', 'slug' => 'custom-products.delete'],
            ['name' => 'مدیریت آیتم‌های محصول سفارشی', 'slug' => 'custom-products.manage-items'],
            ['name' => 'مدیریت قوانین محصول سفارشی', 'slug' => 'custom-products.manage-rules'],

            // Calculation profiles & formulas
            ['name' => 'مشاهده پروفایل‌های محاسبه', 'slug' => 'calculation-profiles.view'],
            ['name' => 'ایجاد پروفایل محاسبه', 'slug' => 'calculation-profiles.create'],
            ['name' => 'ویرایش پروفایل محاسبه', 'slug' => 'calculation-profiles.update'],
            ['name' => 'حذف پروفایل محاسبه', 'slug' => 'calculation-profiles.delete'],
            ['name' => 'مشاهده فرمول‌ها', 'slug' => 'formulas.view'],
            ['name' => 'ایجاد فرمول', 'slug' => 'formulas.create'],
            ['name' => 'ویرایش فرمول', 'slug' => 'formulas.update'],
            ['name' => 'حذف فرمول', 'slug' => 'formulas.delete'],

            // Orders
            ['name' => 'مشاهده همه سفارش‌ها', 'slug' => 'orders.view'],
            ['name' => 'ویرایش سفارش', 'slug' => 'orders.update'],

            // Users
            ['name' => 'مشاهده کاربران', 'slug' => 'users.view'],
            ['name' => 'ایجاد کاربر', 'slug' => 'users.create'],
            ['name' => 'ویرایش کاربر', 'slug' => 'users.update'],
            ['name' => 'حذف کاربر', 'slug' => 'users.delete'],

            // Roles
            ['name' => 'مشاهده نقش‌ها', 'slug' => 'roles.view'],
            ['name' => 'ایجاد نقش', 'slug' => 'roles.create'],
            ['name' => 'ویرایش نقش', 'slug' => 'roles.update'],
            ['name' => 'حذف نقش', 'slug' => 'roles.delete'],

            // Permission
            ['name' => 'مشاهده دسترسی ها', 'slug' => 'permissions.view'],

        ];

        $slugs = array_column($permissions, 'slug');

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        Permission::whereNotIn('slug', $slugs)->delete();
    }
}
