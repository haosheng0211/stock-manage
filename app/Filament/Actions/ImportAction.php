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
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Livewire\TemporaryUploadedFile;
use Vtiful\Kernel\Excel;

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
                ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file) => Carbon::now()->format('Ymd_His') . '.' . $file->extension()),
        ]);
        $this->label('匯入');
        $this->color('success');
        $this->action(function (array $data) {
            $data['file'] = basename($data['file']);
            $document = Document::create([
                'type'    => DocumentType::IMPORT,
                'user_id' => Auth::id(),
                'model'   => Part::class,
                'status'  => DocumentStatus::PROCESS,
            ]);

            ImportJob::dispatch($document, $data);
            $this->success();
        });
        $this->successNotificationTitle('項目匯入完成，請至「檔案」查看結果。');
    }

    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    private function open(string $file): array
    {
        /** @var Excel $excel */
        $excel = app('excel.import');

        $rows = $excel->openFile($file)->setType([
            2  => Excel::TYPE_INT,
            9  => Excel::TYPE_INT,
            10 => Excel::TYPE_INT,
        ])->openSheet()->getSheetData();

        unset($rows[0]);

        return $rows;
    }

    private function format(array $row): array
    {
        return [
            'part_number'       => $row[0],
            'brand'             => $row[1],
            'quantity'          => $row[2],
            'twd_price'         => $row[3],
            'usd_price'         => $row[4],
            'datecode'          => $row[5],
            'leadtime'          => $row[6],
            'package'           => $row[7],
            'description'       => $row[8],
            'contact_people_id' => $row[9],
            'supplier_id'       => $row[10],
        ];
    }

    private function validate(array $format): \Illuminate\Validation\Validator
    {
        return Validator::make($format, [
            'part_number'       => ['required'],
            'quantity'          => ['required', 'numeric', 'min:1'],
            'supplier_id'       => ['required', 'integer', 'exists:suppliers,id'],
            'contact_people_id' => ['required', 'integer', Rule::exists('contact_people', 'id')->where(fn (Builder $builder) => $builder->where('supplier_id', $format['supplier_id']))],
        ]);
    }
}
