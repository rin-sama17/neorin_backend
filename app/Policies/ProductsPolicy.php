<?php

namespace App\Policies;

use App\Models\Product\Products;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductsPolicy
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
    public function view(User $user, Products $product): bool
    {
        return $user->id === $product->user_id;
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
    public function update(User $user, Products $products): bool
    {
        return $user->id === $products->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Products $products): bool
    {
        return $user->id === $products->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Products $products): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Products $products): bool
    {
        return false;
    }
}
