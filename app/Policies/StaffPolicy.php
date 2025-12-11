<?php

namespace App\Policies;

use App\Models\User;

class StaffPolicy
{
    /**
     * Determine if user can view staff
     */
    public function view(User $user, User $staff): bool
    {
        return $user->business_id === $staff->business_id;
    }

    /**
     * Determine if user can update staff
     */
    public function update(User $user, User $staff): bool
    {
        return $user->business_id === $staff->business_id 
            && $user->id !== $staff->id;
    }

    /**
     * Determine if user can delete staff
     */
    public function delete(User $user, User $staff): bool
    {
        return $user->business_id === $staff->business_id 
            && $user->id !== $staff->id
            && $staff->sales()->count() === 0;
    }
}