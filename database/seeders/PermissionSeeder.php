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

            // Product
            ['name' => 'مشاهده محصولات', 'slug' => 'product.view'],
            ['name' => 'ایجاد محصول', 'slug' => 'product.create'],
            ['name' => 'ویرایش محصول', 'slug' => 'product.update'],
            ['name' => 'حذف محصول', 'slug' => 'product.delete'],

            // Category
            ['name' => 'مشاهده دسته بندی', 'slug' => 'category.view'],
            ['name' => 'ایجاد دسته بندی', 'slug' => 'category.create'],
            ['name' => 'ویرایش دسته بندی', 'slug' => 'category.update'],
            ['name' => 'حذف دسته بندی', 'slug' => 'category.delete'],

            // Fabric
            ['name' => 'مشاهده پارچه', 'slug' => 'fabric.view'],
            ['name' => 'ایجاد پارچه', 'slug' => 'fabric.create'],
            ['name' => 'ویرایش پارچه', 'slug' => 'fabric.update'],
            ['name' => 'حذف پارچه', 'slug' => 'fabric.delete'],

            // Color
            ['name' => 'مشاهده رنگ', 'slug' => 'color.view'],
            ['name' => 'ایجاد رنگ', 'slug' => 'color.create'],
            ['name' => 'ویرایش رنگ', 'slug' => 'color.update'],
            ['name' => 'حذف رنگ', 'slug' => 'color.delete'],

            // Size
            ['name' => 'مشاهده سایز', 'slug' => 'size.view'],
            ['name' => 'ایجاد سایز', 'slug' => 'size.create'],
            ['name' => 'ویرایش سایز', 'slug' => 'size.update'],
            ['name' => 'حذف سایز', 'slug' => 'size.delete'],

            // Discount
            ['name' => 'مشاهده تخفیف', 'slug' => 'discount.view'],
            ['name' => 'ایجاد تخفیف', 'slug' => 'discount.create'],
            ['name' => 'ویرایش تخفیف', 'slug' => 'discount.update'],
            ['name' => 'حذف تخفیف', 'slug' => 'discount.delete'],

            // Users
            ['name' => 'مشاهده کاربران', 'slug' => 'user.view'],
            ['name' => 'ویرایش کاربران', 'slug' => 'user.update'],

            // Roles
            ['name' => 'مدیریت نقش‌ها', 'slug' => 'role.manage'],

            // Settings
            ['name' => 'تنظیمات سایت', 'slug' => 'setting.manage'],
        ];

        foreach ($permissions as $permission) {

            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
