<?php

namespace Uccello\ModuleDesigner\View\Components;

use Illuminate\View\Component;

class VerticalStepCard extends Component
{
    public $title;
    public $step;
    public $closed;
    public $after;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $step, $closed = false, $after = null)
    {
        $this->title = $title;
        $this->step = $step;
        $this->closed = $closed;
        $this->after = $after;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('import-manager::components.vertical-step-card');
    }
}
