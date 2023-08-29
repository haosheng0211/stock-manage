<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class Backup extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('backup.host', 'smtp.gmail.com');
        $this->migrator->add('backup.port', '465');
        $this->migrator->add('backup.username', '');
        $this->migrator->add('backup.password', '');
        $this->migrator->add('backup.encryption', 'ssl');
    }
}
