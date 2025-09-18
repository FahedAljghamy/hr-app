<?php

/**
 * Author: Eng.Fahed
 * StatCard Component for HR System
 */

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCard extends Component
{
    public $title;
    public $value;
    public $icon;
    public $color;
    public $percentage;
    public $percentageColor;

    /**
     * Create a new component instance.
     */
    public function __construct($title = '', $value = '0', $icon = 'fas fa-calendar', $color = 'primary', $percentage = '', $percentageColor = 'text-success')
    {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->color = $color;
        $this->percentage = $percentage;
        $this->percentageColor = $percentageColor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.stat-card');
    }
}
