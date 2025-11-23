<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get the currently active exam
        $activeExam = Exam::where('is_active', true)->first();

        return view('admission.settings.index', compact('activeExam'));
    }
}

