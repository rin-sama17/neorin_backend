<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'mobile_verified_at' => $this->mobile_verified_at,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('slug')),
            'permissions' => $this->whenLoaded('roles', function () {
                return $this->roles
                    ->flatMap(fn ($role) => $role->permissions->pluck('slug'))
                    ->unique()
                    ->values();
            }),
            'created_at' => $this->created_at,
        ];
    }
}
