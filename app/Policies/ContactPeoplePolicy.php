<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\ContactPeople;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPeoplePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionAny([
            UserPermission::VIEW_CONTACT_PEOPLE,
            UserPermission::CREATE_CONTACT_PEOPLE,
            UserPermission::UPDATE_CONTACT_PEOPLE,
            UserPermission::DELETE_CONTACT_PEOPLE,
            UserPermission::EXPORT_CONTACT_PEOPLE,
        ]);
    }

    public function view(User $user, ContactPeople $contactPeople): bool
    {
        return $user->hasPermission(UserPermission::VIEW_CONTACT_PEOPLE);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermission::CREATE_CONTACT_PEOPLE);
    }

    public function update(User $user, ContactPeople $contactPeople): bool
    {
        return $user->hasPermission(UserPermission::UPDATE_CONTACT_PEOPLE);
    }

    public function delete(User $user, ContactPeople $contactPeople): bool
    {
        return $user->hasPermission(UserPermission::DELETE_CONTACT_PEOPLE);
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasPermission(UserPermission::DELETE_CONTACT_PEOPLE);
    }

    public function export(User $user): bool
    {
        return $user->hasPermission(UserPermission::EXPORT_CONTACT_PEOPLE);
    }
}
