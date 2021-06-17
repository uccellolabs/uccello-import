<?php

namespace Uccello\Import\Support;

trait StepManager
{
    public $step = 0;

    public function changeStep($step)
    {
        $this->emit('stepChanged', $step);
    }

    public function incrementStep()
    {
        $this->changeStep($this->step + 1);
    }

    public function onStepChanged($step)
    {
        $this->step = $step;
    }

    public function isUploadingFile()
    {
        return $this->step === 0;
    }

    public function isConfiguringMapping()
    {
        return $this->step === 1;
    }
}
