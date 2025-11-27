<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApplicantScheduleController extends Controller
{
    /**
     * Display the applicant's exam schedule with exam access logic.
     */
    public function index()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Get the assigned schedule (if any) with exam details
        $assignedSchedule = $applicant->examSchedules()
            ->with(['examSchedule.exam'])
            ->latest()
            ->first();

        // Initialize variables
        $hasSchedule = false;
        $examAvailable = false;
        $examExpired = false;
        $examUpcoming = false;
        $finishedAttempt = null;
        $startTimePh = null;
        $endTimePh = null;
        $nowPh = null;

        if ($assignedSchedule) {
            $hasSchedule = true;
            $schedule = $assignedSchedule->examSchedule;
            $exam = $schedule->exam;

            // All times are treated as Asia/Manila (app timezone)
            $nowPh = Carbon::now();
            
            // Combine schedule_date with start_time and end_time
            // Use toDateString() to get YYYY-MM-DD without time component
            $examDate = $schedule->schedule_date->toDateString();
            $startTimePh = Carbon::parse($examDate . ' ' . $schedule->start_time);
            $endTimePh = Carbon::parse($examDate . ' ' . $schedule->end_time);
            
            // Temporary debug log
            \Log::debug("ScheduleController - NOW=$nowPh | START=$startTimePh | END=$endTimePh");

            // Check if applicant already has a finished attempt
            $finishedAttempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
                ->where('exam_id', $exam->exam_id)
                ->whereNotNull('finished_at')
                ->first();

            if (!$finishedAttempt) {
                // Check time window
                if ($nowPh->lt($startTimePh)) {
                    $examUpcoming = true;
                } elseif ($nowPh->gt($endTimePh)) {
                    $examExpired = true;
                } else {
                    // Check if already has an unfinished attempt (in progress)
                    $existingAttempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
                        ->where('exam_id', $exam->exam_id)
                        ->whereNull('finished_at')
                        ->first();

                    if ($existingAttempt) {
                        // Exam in progress - should redirect to exam taking page
                        return redirect()->route('applicant.exam.take')
                            ->with('info', 'Resuming your exam...');
                    }

                    // Exam is available to start (within time window and no finished attempt)
                    $examAvailable = $nowPh->gte($startTimePh) && $nowPh->lte($endTimePh) && !$finishedAttempt;
                }
            }
        }

        return view('applicant.schedule', compact(
            'applicant',
            'assignedSchedule',
            'hasSchedule',
            'examAvailable',
            'examExpired',
            'examUpcoming',
            'finishedAttempt',
            'startTimePh',
            'endTimePh',
            'nowPh'
        ));
    }
}

