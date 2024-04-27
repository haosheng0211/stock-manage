<?php

namespace App\Filament\Actions;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Actions\Action;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\TemporaryUploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ImportAction extends Action
{
    use CanCustomizeProcess;

    public string $importable;

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
                'model'         => $this->getModelName(),
                'original_file' => [
                    $data['file'],
                ],
                'status'        => DocumentStatus::PROCESS,
            ]);

            Excel::import(app($this->importable, ['document' => $document]), $data['file']);
            //
            //            try {
            //
            //                $this->success();
            //            } catch (\Throwable $exception) {
            //                Log::error('匯入失敗', [
            //                    'exception' => $exception,
            //                ]);
            //                $document->update([
            //                    'status'        => DocumentStatus::FAILURE,
            //                    'error_message' => $exception->getMessage(),
            //                ]);
            //                $this->failureNotificationTitle('項目匯入失敗，請至「檔案」查看結果。');
            //                $this->failure();
            //            }
        });
        $this->successNotificationTitle('項目匯入完成，請至「檔案」查看結果。');
    }

    public function importable(string $importable): static
    {
        $this->importable = $importable;

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    public function getModelName(): string
    {
        $manage = get_class($this->livewire);

        return $manage::getResource()::getModel();
    }
}
