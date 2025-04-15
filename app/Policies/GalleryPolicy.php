<?php

namespace App\Policies;

use App\Models\Product\Gallery;
use App\Models\Product\Products;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GalleryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Gallery $gallery)
    {
        return $user->id == $gallery->product->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Products $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Gallery $gallery): bool
    {
        return $user->id === $gallery->product->user_id;;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Gallery $gallery): bool
    {
        return $user->id === $gallery->product->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Gallery $gallery): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Gallery $gallery): bool
    {
        return false;
    }
}
