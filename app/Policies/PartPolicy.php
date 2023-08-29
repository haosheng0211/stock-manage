<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\Part;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionAny([
            UserPermission::VIEW_PARTS,
            UserPermission::CREATE_PARTS,
            UserPermission::UPDATE_PARTS,
            UserPermission::DELETE_PARTS,
        ]);
    }

    public function view(User $user, Part $part): bool
    {
        return $user->hasPermission(UserPermission::VIEW_PARTS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermission::CREATE_PARTS);
    }

    public function update(User $user, Part $part): bool
    {
        return $user->hasPermission(UserPermission::UPDATE_PARTS);
    }

    public function bulkUpdate(User $user): bool
    {
        return $user->hasPermission(UserPermission::UPDATE_PARTS);
    }

    public function delete(User $user, Part $part): bool
    {
        return $user->hasPermission(UserPermission::DELETE_PARTS);
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasPermission(UserPermission::DELETE_PARTS);
    }

    public function export(User $user): bool
    {
        return $user->hasPermission(UserPermission::EXPORT_PARTS);
    }

    public function import(User $user): bool
    {
        return $user->hasPermission(UserPermission::IMPORT_PARTS);
    }
}
