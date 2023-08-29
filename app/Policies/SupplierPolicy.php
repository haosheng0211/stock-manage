<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionAny([
            UserPermission::VIEW_SUPPLIERS,
            UserPermission::CREATE_SUPPLIERS,
            UserPermission::UPDATE_SUPPLIERS,
            UserPermission::DELETE_SUPPLIERS,
            UserPermission::EXPORT_SUPPLIERS,
        ]);
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission(UserPermission::VIEW_SUPPLIERS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermission::CREATE_SUPPLIERS);
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission(UserPermission::UPDATE_SUPPLIERS);
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission(UserPermission::DELETE_SUPPLIERS);
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasPermission(UserPermission::DELETE_SUPPLIERS);
    }

    public function export(User $user): bool
    {
        return $user->hasPermission(UserPermission::EXPORT_SUPPLIERS);
    }
}
