<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@admin.cc'], [
            'name'        => '系統管理員',
            'password'    => Hash::make('admin888'),
            'permissions' => UserPermission::getValues(),
            'super_admin' => true,
        ]);
    }
}
