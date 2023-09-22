<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class UserPermission extends Enum implements LocalizedEnum
{
    public const VIEW_USERS = 'view-users';

    public const CREATE_USERS = 'create-users';

    public const UPDATE_USERS = 'update-users';

    public const DELETE_USERS = 'delete-users';

    public const VIEW_PARTS = 'view-parts';

    public const CREATE_PARTS = 'create-parts';

    public const UPDATE_PARTS = 'update-parts';

    public const DELETE_PARTS = 'delete-parts';

    public const EXPORT_PARTS = 'export-parts';

    public const IMPORT_PARTS = 'import-parts';

    public const VIEW_SUPPLIERS = 'view-suppliers';

    public const CREATE_SUPPLIERS = 'create-suppliers';

    public const UPDATE_SUPPLIERS = 'update-suppliers';

    public const DELETE_SUPPLIERS = 'delete-suppliers';

    public const EXPORT_SUPPLIERS = 'export-suppliers';

    public const VIEW_CONTACT_PEOPLE = 'view-contact-people';

    public const CREATE_CONTACT_PEOPLE = 'create-contact-people';

    public const UPDATE_CONTACT_PEOPLE = 'update-contact-people';

    public const DELETE_CONTACT_PEOPLE = 'delete-contact-people';

    public const EXPORT_CONTACT_PEOPLE = 'export-contact-people';

    public const SETTING_BACKUP = 'setting-backup';
}
