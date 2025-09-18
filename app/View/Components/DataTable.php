<?php

/**
 * Author: Eng.Fahed
 * DataTable Component for HR System
 */

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    public $headers;
    public $title;
    public $icon;
    public $color;

    /**
     * Create a new component instance.
     */
    public function __construct($headers = [], $title = '', $icon = 'fas fa-table', $color = 'primary')
    {
        $this->headers = $headers;
        $this->title = $title;
        $this->icon = $icon;
        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.data-table');
    }
}
