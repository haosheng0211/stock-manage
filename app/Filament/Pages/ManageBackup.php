<?php

namespace App\Filament\Pages;

use App\Settings\BackupSetting;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;

class ManageBackup extends SettingsPage
{
    protected static ?string $slug = 'backup';

    protected ?string $heading = '備份';

    protected static string $settings = BackupSetting::class;

    protected static ?string $navigationGroup = '系統';

    protected static ?string $navigationLabel = '備份';

    protected function getFormSchema(): array
    {
        return [
            Card::make([
                TextInput::make('host')
                    ->label('主機')
                    ->required(),
                TextInput::make('port')
                    ->label('端口')
                    ->required(),
                TextInput::make('username')
                    ->label('帳號')
                    ->required(),
                TextInput::make('password')
                    ->label('密碼')
                    ->helperText(new HtmlString('若使用 Gmail，請至 <a style="color: #1d4ed8" href="https://myaccount.google.com/apppasswords" target="_blank">Google 帳號</a> 產生應用程式密碼'))
                    ->required(),
                Select::make('encryption')
                    ->label('加密方式')
                    ->options([
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                    ])
                    ->required(),
            ]),
        ];
    }
}
