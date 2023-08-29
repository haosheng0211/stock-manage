<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionAny([
            UserPermission::EXPORT_PARTS,
            UserPermission::EXPORT_SUPPLIERS,
            UserPermission::EXPORT_CONTACT_PEOPLE,
        ]);
    }
}
