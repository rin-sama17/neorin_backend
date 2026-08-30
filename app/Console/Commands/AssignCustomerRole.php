<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\User\Role;
use Illuminate\Console\Command;

class AssignCustomerRole extends Command
{
    protected $signature = 'roles:assign-user {--force : assign to all users, not only role-less ones}';

    protected $description = 'Assign the user role to users that have no roles yet';

    public function handle(): int
    {
        $user = Role::where('slug', 'user')->first();

        if (!$user) {
            $this->error('نقش مشتری پیدا نشد. ابتدا RoleSeeder را اجرا کنید.');

            return self::FAILURE;
        }

        $query = User::query();

        if (!$this->option('force')) {
            $query->whereDoesntHave('roles');
        }

        $users = $query->get();

        foreach ($users as $user) {
            $user->roles()->syncWithoutDetaching($user->id);
        }

        $this->info("نقش مشتری به {$users->count()} کاربر اختصاص یافت.");

        return self::SUCCESS;
    }
}
