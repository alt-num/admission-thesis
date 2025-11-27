<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicantDashboardController extends Controller
{
    /**
     * Display the applicant dashboard.
     */
    public function index()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Get the assigned schedule (if any)
        $assignedSchedule = $applicant->examSchedules()
            ->with('examSchedule.exam')
            ->latest()
            ->first();

        // Get the latest exam attempt (if any)
        $examAttempt = $applicant->examAttempts()
            ->with('exam')
            ->latest()
            ->first();

        // Get course evaluation results (if any)
        $courseResults = $applicant->courseResults()
            ->with('course')
            ->get();

        // Determine exam status
        $examStatus = 'not_started';
        if ($examAttempt) {
            if ($examAttempt->finished_at) {
                $examStatus = 'finished';
            } else {
                $examStatus = 'in_progress';
            }
        }

        return view('applicant.dashboard', compact(
            'applicant',
            'assignedSchedule',
            'examAttempt',
            'courseResults',
            'examStatus'
        ));
    }
}

