<?php

/**
 * Author: Eng.Fahed
 * Card Component for HR System
 */

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public $title;
    public $icon;
    public $color;
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct($title = '', $icon = '', $color = 'primary', $class = '')
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->color = $color;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card');
    }
}
