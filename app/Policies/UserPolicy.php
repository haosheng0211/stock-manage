<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionAny([
            UserPermission::VIEW_USERS,
            UserPermission::CREATE_USERS,
            UserPermission::UPDATE_USERS,
            UserPermission::DELETE_USERS,
        ]);
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission(UserPermission::VIEW_USERS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermission::CREATE_USERS);
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission(UserPermission::UPDATE_USERS);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermission(UserPermission::DELETE_USERS);
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasPermission(UserPermission::DELETE_USERS);
    }
}
