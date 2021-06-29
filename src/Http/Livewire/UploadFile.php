<?php

namespace Uccello\Import\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Uccello\Import\Support\StepManager;

class UploadFile extends Component
{
    use WithFileUploads, StepManager;

    public $module;
    public $file;
    public $config = [
        'firstRow' => 2,
        'withHeader' => true
    ];

    protected $listeners = [
        'stepChanged' => 'onStepChanged',
    ];

    public function render()
    {
        return view('import::livewire.upload-file');
    }

    public function uploadFile()
    {
        $this->validate([
            'config.firstRow' => 'integer|required',
            'file' => 'file|max:'.$this->getMaximumFileUploadSize()
        ]);

        $relativePath = $this->file->store('import');
        $filePath = storage_path('app/' . $relativePath);

        $this->emit('filePathChanged', $filePath);

        $this->incrementStep();
    }

    public function toggleWithHeader()
    {
        $this->config['withHeader'] = !$this->config['withHeader'];

        if (!$this->config['withHeader']) {
            $this->config['firstRow'] = 1;
        } else {
            $this->config['firstRow'] = 2;
        }
    }

    public function incrementStep()
    {
        $this->changeStep($this->step + 1);

        $this->emit('configChanged', $this->config);
    }

    /**
    * This function returns the maximum files size that can be uploaded
    * in PHP
    * @returns int File size in bytes
    **/
    private function getMaximumFileUploadSize()
    {
        return min(
            $this->convertPHPSizeToBytes(ini_get('post_max_size')),
            $this->convertPHPSizeToBytes(ini_get('upload_max_filesize'))
        );
    }

    /**
    * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
    *
    * @param string $sSize
    * @return integer The value in bytes
    */
    private function convertPHPSizeToBytes($sSize)
    {
        $sSuffix = strtoupper(substr($sSize, -1));
        if (!in_array($sSuffix, ['P', 'T', 'G', 'M' ,'K'])) {
            return (int)$sSize;
        }

        $iValue = substr($sSize, 0, -1);
        switch ($sSuffix) {
            case 'P':
                $iValue *= 1024;
                // Fallthrough intended
            case 'T':
                $iValue *= 1024;
                // Fallthrough intended
            case 'G':
                $iValue *= 1024;
                // Fallthrough intended
            case 'M':
                $iValue *= 1024;
                // Fallthrough intended
            case 'K':
                $iValue *= 1024;
                break;
        }

        return (int)$iValue;
    }
}
