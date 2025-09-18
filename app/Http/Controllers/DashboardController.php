<?php

/**
 * Author: Eng.Fahed
 * Dashboard Controller for HR System
 * Handles dashboard functionality and statistics
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page with statistics
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Sample statistics data
        $stats = [
            'total_employees' => 156,
            'total_departments' => 12,
            'active_positions' => 89,
            'pending_leaves' => 23
        ];

        return view('dashboard', compact('stats'));
    }
}
