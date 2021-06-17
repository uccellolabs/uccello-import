<?php

namespace Uccello\Import\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\HeadingRowImport;
use Uccello\Core\Models\Module;
use Uccello\Import\Imports\GenericImport;
use Uccello\Import\Jobs\NotifyUserOfCompletedImport;
use Uccello\Import\Support\StepManager;

class ConfigMapping extends Component
{
    use StepManager;

    public $domain;
    public $module;
    public $config;

    public $filePath = "/var/www/html/storage/app/import/qQzhzfWvRkpnxHzxxW0SYPPEmPOkOPfpTEQXUV6o.csv";

    protected $listeners = [
        'stepChanged' => 'onStepChanged',
        'filePathChanged' => 'onFilePathChanged',
    ];

    private $filteredFields = [];

    public function render()
    {
        return view('import::livewire.config-mapping', [
            'fields' => $this->getModuleFields(),
            'headings' => $this->getFileHeadings(),
            'firstRow' => $this->getFileFirstRow(),
            'filteredFields' => $this->filteredFields
        ]);
    }

    public function onFilePathChanged($filePath)
    {
        $this->filePath = $filePath;
    }

    public function filterFields($headingsCount)
    {
        $this->filteredFields = [];

        if ($this->config) {
            for ($i = 0; $i < $headingsCount; $i++) {
                if (!empty($this->config[$i])) {
                    $this->filteredFields[] = $this->config[$i]['field'];
                }
            }

            $this->filteredFields = array_unique($this->filteredFields);
        }
    }

    public function launchImport()
    {
        (new GenericImport($this->domain, $this->module, $this->config, [], []))
            ->queue($this->filePath)->chain([
                new NotifyUserOfCompletedImport(auth()->user(), uctrans($this->module->name, $this->module)),
            ]);
    }

    private function getFileHeadings()
    {
        if (empty($this->filePath)) {
            return null;
        }

        $headings = (new HeadingRowImport(1))->toArray($this->filePath, null, $this->getReaderType());
        if (!empty($headings[0][0])) { // First Sheet
            $headings = $headings[0][0];
        }

        return $headings;
    }

    private function getFileFirstRow()
    {
        if (empty($this->filePath)) {
            return null;
        }

        $firstRow = (new HeadingRowImport(2))->toArray($this->filePath, null, $this->getReaderType());
        if (!empty($firstRow[0][0])) {
            $firstRow = $firstRow[0][0];
        }

        return $firstRow;
    }

    private function getReaderType()
    {
        return \Maatwebsite\Excel\Excel::CSV;
    }

    private function getModuleFields()
    {
        return $this->module->fields;
    }
}
