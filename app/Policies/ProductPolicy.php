<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;


class ProductPolicy
{
    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view products
        return true;
    }

    /**
     * Determine if the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        // All authenticated users can view a product
        return true;
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        // Admins and managers can create products
        return $user->hasAdminPrivileges();
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        // Admins and managers can update all products
        // Staff can only view but not update
        return $user->hasAdminPrivileges();
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Admins and managers can delete products
        return $user->hasAdminPrivileges();
    }

    /**
     * Determine if the user can restore the product.
     */
    public function restore(User $user, Product $product): bool
    {
        // Admins and managers can restore products
        return $user->hasAdminPrivileges();
    }

    /**
     * Determine if the user can permanently delete the product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        // Admins and managers can force delete products
        return $user->hasAdminPrivileges();
    }

    /**
     * Determine if the user can adjust stock.
     */
    public function adjustStock(User $user, Product $product): bool
    {
        // Admin, manager and staff can adjust stock
        return $user->hasAdminPrivileges() || $user->isStaff();
    }
}


