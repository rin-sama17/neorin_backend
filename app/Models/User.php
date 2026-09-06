<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\User\Role;
use App\Models\Product\Products;
use App\Models\Shop\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User\Address;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'mobile_verified_at',
        'city_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected $appends = [
        'is_admin',
    ];

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin()
            || $this->rolePermissionSlugs()->isNotEmpty();
    }
    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }
    public function products()
    {
        return $this->hasMany(Products::class);
    }
    public function favoriteProducts()
    {
        return $this->belongsToMany(Products::class)->withTimestamps();
    }
    public function viewedProducts()
    {
        return $this->belongsToMany(Products::class, 'products_view_history')->withTimestamps();
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function permissions()
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn(Role $role) => $role->permissions);
    }

    public function hasRole(string $role): bool
    {
        if ($this->rolesRelationIsLoaded()) {
            return $this->roles->contains(fn(Role $r) => $r->slug === $role);
        }

        return $this->roles()
            ->where('slug', $role)
            ->exists();
    }

    public function hasAnyRole(string ...$roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllRoles(string ...$roles): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->rolePermissionSlugs()->contains($permission);
    }

    public function hasAnyPermission(string ...$permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->rolePermissionSlugs()->intersect($permissions)->isNotEmpty();
    }

    public function hasAllPermissions(string ...$permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->rolePermissionSlugs()->intersect($permissions)->count() === count($permissions);
    }

    private function rolesRelationIsLoaded(): bool
    {
        return array_key_exists('roles', $this->relations);
    }

    private function rolePermissionSlugs(): \Illuminate\Support\Collection
    {
        if (!array_key_exists('permission_slugs', $this->relations)) {
            if ($this->rolesRelationIsLoaded()) {
                $this->load('roles.permissions');
                $slugs = $this->roles->flatMap(fn(Role $role) => $role->permissions->pluck('slug'));
            } else {
                $slugs = $this->roles()
                    ->with('permissions')
                    ->get()
                    ->flatMap(fn(Role $role) => $role->permissions->pluck('slug'));
            }

            $this->setRelation('permission_slugs', $slugs);
        }

        return $this->relations['permission_slugs'];
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
