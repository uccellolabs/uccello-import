<?php

namespace Uccello\Import\Imports;

use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Uccello\Core\Models\Domain;
use Uccello\Import\Notifications\ImportHasFailedNotification;
use Uccello\Import\Notifications\ImportIsReadyNotification;
use Uccello\Import\Support\WithStatsTrait;

class GenericImport implements ToModel, WithStartRow, WithChunkReading, ShouldQueue, WithEvents
{
    use Importable;
    use WithStatsTrait;

    protected $domain;
    protected $module;
    protected $fields;
    protected $configs;
    protected $defaultValues;

    public function __construct($domain, $module, $fields, $configs, $defaultValues)
    {
        $this->importedBy = auth()->user();
        $this->domain = $domain;
        $this->module = $module;
        $this->fields = $fields;
        $this->configs = $configs;
        $this->defaultValues = $defaultValues;

        // Pour la console on récupère l'utilisateur par défaut
        if (!$this->importedBy && env('IMPORT_DEFAULT_USER')) {
            $this->importedBy = User::where('name', env('IMPORT_DEFAULT_USER'))->first();
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $this->notificationData = function () {
                    return 42;
                };

                $data = $this->getFromCacheAndClear();

                if ($this->importedBy) {
                    $this->importedBy->notify(new ImportIsReadyNotification(uctrans('account', ucmodule('account')), $data));
                }
            },

            ImportFailed::class => function (ImportFailed $event) {
                if ($this->importedBy) {
                    $this->importedBy->notify(new ImportHasFailedNotification(uctrans('account', ucmodule('account'))));
                }
            },
        ];
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @param string|UploadedFile|null $filePath
     * @param string|null              $disk
     * @param string|null              $readerType
     *
     * @throws NoFilePathGivenException
     * @return Importer|PendingDispatch
     */
    public function import($filePath = null, string $disk = null, string $readerType = null)
    {
        $filePath = $this->getFilePath($filePath);

        $this->initCache($filePath);

        return $this->getImporter()->import(
            $this,
            $filePath,
            $disk ?? $this->disk ?? null,
            $readerType ?? $this->readerType ?? null
        );
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $modelClass = $this->module->model_class;
        $record = new $modelClass;

        foreach ($row as $i => $column) {
            $fieldName = $this->fields[$i];
            $config = json_decode($this->configs[$i]);
            $field = $this->module->fields()->where('name', $fieldName)->first();

            $value = $row[$i] ?? $this->defaultValues[$i];
            $record->{$field->column} = uitype($field->uitype_id)->getFormattedValueToSaveWithConfig(request(), $field, $value, $config, $record, $this->domain, $this->module);
        }

        // Add domain_id if necessary
        if (Schema::hasColumn($record->getTable(), 'domain_id')) {
            $record->domain_id = $this->domain->getKey();
        }

        // $record->getKey()
        //     ? $this->notificationData['updated']++
        //     : $this->notificationData['created']++;

        // $this->notificationData['created']++;
        // $this->notificationData['lines']++;

        return $record;
    }
}
