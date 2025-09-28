<?php

namespace App\Policies;

use App\Models\Office;
use App\Models\User;

class OfficePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Office $office): bool
    {
        return $user->office_id === $office->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Office $office): bool
    {
        return $user->isAdmin() && $user->office_id === $office->id;
    }

    public function delete(User $user, Office $office): bool
    {
        return $user->isAdmin() && $user->office_id === $office->id;
    }
}