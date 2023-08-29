<?php

namespace App\Filament\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EditBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    protected function setUp(): void
    {
        parent::setUp();
        $this->icon('heroicon-s-pencil');
        $this->color('primary');
        $this->label('編輯所選項目');
        $this->action(function () {
            $this->process(function (Collection $records, array $data) {
                $data = array_filter($data, static fn ($value) => ! blank($value));

                if (count($data)) {
                    $records->each(fn (Model $record) => $record->update($data));
                }
            });

            $this->success();
        });
        $this->successNotificationTitle('項目已編輯');
        $this->deselectRecordsAfterCompletion();
    }

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }
}
