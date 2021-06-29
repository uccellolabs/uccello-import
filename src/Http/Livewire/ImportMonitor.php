<?php

namespace Uccello\Import\Http\Livewire;

use Livewire\Component;
use Uccello\Import\Models\Import;
use Uccello\Import\Support\StepManager;

class ImportMonitor extends Component
{
    use StepManager;

    public $domain;
    public $module;

    public $importId;
    public $importData;

    protected $listeners = [
        'importLaunched' => 'onImportLaunched',
    ];

    public function render()
    {
        $import = Import::find($this->importId);

        if ($import) {
            $this->importData = (array) $import->data;
        }

        return view('import::livewire.import-monitor');
    }

    public function onImportLaunched($importId)
    {
        $this->importId = $importId;
    }
}
