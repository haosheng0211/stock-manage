<?php

namespace App\Filament\Actions;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Jobs\ImportJob;
use App\Models\Document;
use App\Models\Part;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Actions\Action;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\TemporaryUploadedFile;

class ImportAction extends Action
{
    use CanCustomizeProcess;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form([
            FileUpload::make('file')
                ->hint(fn () => new HtmlString('<a href="' . Storage::url('documents/imports/part-example.xlsx') . '" target="_blank" style="color: rgb(59 130 246 / var(--tw-text-opacity));">範例檔案</a>'))
                ->label(trans('validation.attributes.file'))
                ->directory(config('excel.import.directory'))
                ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file) => $file->getClientOriginalName()),
        ]);
        $this->label('匯入');
        $this->color('success');
        $this->action(function (array $data) {
            $document = Document::create([
                'type'          => DocumentType::IMPORT,
                'user_id'       => Auth::id(),
                'model'         => Part::class,
                'original_file' => is_array($data['file']) ? $data['file'] : [$data['file']],
                'status'        => DocumentStatus::PROCESS,
            ]);

            ImportJob::dispatch($document, array_merge($data, [
                'file' => basename($data['file']),
            ]));
            $this->success();
        });
        $this->successNotificationTitle('項目匯入完成，請至「檔案」查看結果。');
    }

    public static function getDefaultName(): ?string
    {
        return 'import';
    }
}
