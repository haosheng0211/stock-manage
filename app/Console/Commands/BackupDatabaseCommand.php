<?php

namespace App\Console\Commands;

use App\Mail\BackupDatabaseMail;
use app\Settings\BackupSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Backup the database and send to email';

    public function handle(): void
    {
        $this->loadMailConfig();

        $info = config('database.connections.mysql');
        $file = storage_path('app/backups/backup_' . Carbon::now()->format('YmdHis') . '.sql');

        exec('mysqldump -u ' . $info['username'] . ' -p' . $info['password'] . ' ' . $info['database'] . ' > ' . $file);

        Mail::to(config('mail.from.address'))->send(new BackupDatabaseMail($file));
    }

    public function loadMailConfig(): void
    {
        $setting = app(BackupSetting::class);
        config()->set('mail.mailers.smtp.host', $setting->host);
        config()->set('mail.mailers.smtp.port', $setting->port);
        config()->set('mail.mailers.smtp.username', $setting->username);
        config()->set('mail.mailers.smtp.password', $setting->password);
        config()->set('mail.mailers.smtp.encryption', $setting->encryption);
        config()->set('mail.from.address', $setting->username);
        config()->set('mail.from.name', config('app.name'));
    }
}
