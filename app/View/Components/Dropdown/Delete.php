<?php

namespace App\View\Components\Dropdown;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Delete extends Component
{
    public $route;
    public $id;
    public $permission;
    public $label;
    /**
     * Create a new component instance.
     */
    public function __construct($route, $id, $permission = null, $label = 'Delete')
    {
        $this->route = $route;
        $this->id = $id;
        $this->permission = $permission;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown.delete');
    }
}
