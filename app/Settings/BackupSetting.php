<?php

namespace app\Settings;

use Spatie\LaravelSettings\Settings;

class BackupSetting extends Settings
{
    public string $host;

    public string $port;

    public string $username;

    public string $password;

    public string $encryption;

    public static function group(): string
    {
        return 'backup';
    }
}
