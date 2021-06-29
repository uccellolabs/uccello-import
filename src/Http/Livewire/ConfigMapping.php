<?php

namespace Uccello\Import\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use stdClass;
use Uccello\Core\Models\Module;
use Uccello\Import\Imports\GenericImport;
use Uccello\Import\Jobs\NotifyUserOfCompletedImport;
use Uccello\Import\Models\Import;
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
        'configChanged' => 'onConfigChanged',
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

    public function onConfigChanged($config)
    {
        $this->config = $config;

        $this->initConfig();
    }

    public function filterFieldsAndShowOptions($headingsCount, $i)
    {
        $this->filterFields($headingsCount);

        $this->showOptions($i);
    }

    public function launchImport()
    {
        $import = Import::create([
            'domain_id' => $this->domain->getKey(),
            'module_id' => $this->module->getKey(),
            'user_id' => Auth::id(),
            'config' => $this->config,
            'data' => [
                'total' => $this->getNumberOfRows(),
                'imported' => 0,
                'success' => 0,
                'error' => 0
            ]
        ]);

        $this->emit('importLaunched', $import->getKey());

        (new GenericImport($import->getKey()))
            ->queue($this->filePath)->chain([
                new NotifyUserOfCompletedImport(auth()->user(), uctrans($this->module->name, $this->module)),
            ]);
    }

    private function initConfig()
    {
        if (empty($this->config)) {
            $this->config = [
                "fields" => []
            ];
        }

        $headings = $this->getFileHeadings();

        foreach ($headings as $i => $heading) {
            $this->config["fields"][$i] = [
                'field' => null
            ];
        }
    }

    private function getNumberOfRows()
    {
        $objPHPExcel = IOFactory::load($this->filePath);

        return $objPHPExcel->setActiveSheetIndex(0)->getHighestDataRow() - $this->config['firstRow'] + 1;
    }

    private function filterFields($headingsCount)
    {
        $this->filteredFields = [];

        if ($this->config) {
            for ($i = 0; $i < $headingsCount; $i++) {
                if (!empty($this->config['fields'][$i])) {
                    $this->filteredFields[] = $this->config['fields'][$i]['field'];
                }
            }

            $this->filteredFields = array_unique($this->filteredFields);
        }
    }

    private function showOptions($i)
    {
        $fieldName = optional($this->config['fields'][$i])['field'];
        $field = $this->module->fields()->where('name', $fieldName)->first();

        $this->config['fields'][$i]['options'] = $this->getUitypeFieldOptions($field);
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

    private function getUitypeFieldOptions($field)
    {
        $options = $this->getFieldOptionsAccordingToUitype($field);

        if ($options) {
            foreach ($options as $i => $option) {
                foreach ($option as $key => $value) {
                    if ($this->isClosure($value)) {
                        $options[$i][$key] = call_user_func($value);
                    }
                }
            }
        }

        return $options;
    }

    private function getFieldOptionsAccordingToUitype($field)
    {
        if (!$field) {
            return null;
        }

        $bundle = $this->makeBundle($field);
        $uitypeInstance = $this->getUitypeInstance($field);

        return $uitypeInstance->getFieldOptionsForImport($bundle);
    }

    private function makeBundle($field)
    {
        $bundle = new stdClass;
        $bundle->field = (object) $field;
        // $bundle->inputFields = collect($this->fields);

        return $bundle;
    }

    private function getUitypeInstance($field)
    {
        $uitype = $this->getFieldUitype($field);

        return (new ($uitype->class));
    }

    private function getFieldUitype($field)
    {
        if (empty($field)) {
            return null;
        }

        return uitype($field->uitype_id);
    }

    private function isClosure($value)
    {
        return $value instanceof \Closure;
    }

    private function updateFieldOptions($field)
    {
        foreach ($field['options'] as $option) {
            $field = $this->updateFieldDataIfOptionHasDefaultValue($field, $option);

            $field = $this->addFirstRowForOptionOfTypeArray($field, $option);
        }

        return $field;
    }

    private function updateFieldDataIfOptionHasDefaultValue($field, $option)
    {
        if ($this->isOptionDefaultValueDefined($option)) {
            $field['data'][$option['key']] = $option['default'];
        }

        return $field;
    }

    private function isOptionDefaultValueDefined($option)
    {
        return isset($option['default']);
    }

    private function addFirstRowForOptionOfTypeArray($field, $option)
    {
        if ($this->isOptionOfTypeArray($option)) {
            $defaultRow = new stdClass;
            $defaultRow->value = '';
            $defaultRow->label = '';

            $field['data'][$option['key']] = [
                $defaultRow
            ];
        }

        return $field;
    }

    private function isOptionOfTypeArray($option)
    {
        return $option['type'] === 'array';
    }
}
