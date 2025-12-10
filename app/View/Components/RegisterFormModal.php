<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RegisterFormModal extends Component
{
    public $system;

    /**
     * Create a new component instance.
     */
    public function __construct($system = [])
    {
        $this->system = $system;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.register-form-modal');
    }
}

