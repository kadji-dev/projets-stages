<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Cursus;
use App\Models\Field;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'years' => AcademicYear::count(),
            'fields' => Field::count(),
            'cursuses' => Cursus::count(),
        ];

        $currentYear = AcademicYear::where('is_current', true)->first();

        return view('staff-dashboard.dashboard', compact('stats', 'currentYear'));
    }
}
