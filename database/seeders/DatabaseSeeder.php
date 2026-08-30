<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $user = User::firstOrCreate(
            ['email' => 'okumura.rin.samaa@gmail.com'],
            ['mobile' => '09981393389'],
            ['name' => 'hossein']
        );

        $superAdmin = Role::where('slug', 'super-admin')->first();
        $user->roles()->syncWithoutDetaching($superAdmin);
    }
}
