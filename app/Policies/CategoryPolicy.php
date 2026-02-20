<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories.
     * All authenticated users can view categories.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return in_array($user->role, ['admin', 'manager', 'staff']);
    }

    /**
     * Determine if the user can create categories.
     * Only admins and managers can create categories.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine if the user can update categories.
     * Only admins can update categories.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete categories.
     * Only admins and managers can delete categories.
     */
    public function delete(User $user, Category $category): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }
}
