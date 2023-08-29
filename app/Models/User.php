<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;

    protected $casts = [
        'permissions' => 'array',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
    ];

    public function canAccessFilament(): bool
    {
        return true;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function hasPermissionAny(array $permissions): bool
    {
        return count(array_intersect($permissions, $this->permissions ?? [])) > 0;
    }
}
