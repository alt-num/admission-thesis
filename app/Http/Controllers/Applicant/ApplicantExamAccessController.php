<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicantExamAccessController extends Controller
{
    /**
     * Display the exam access gate page.
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

        // Check if no schedule assigned
        if (!$assignedSchedule) {
            return view('applicant.exam_access', [
                'applicant' => $applicant,
                'assignedSchedule' => null,
                'status' => 'no_schedule',
                'message' => 'You have no exam schedule assigned.',
            ]);
        }

        $schedule = $assignedSchedule->examSchedule;
        $exam = $schedule->exam;

        // Get current date and time
        $now = Carbon::now();
        $scheduleDate = Carbon::parse($schedule->schedule_date->format('Y-m-d'));
        $startDateTime = Carbon::parse($scheduleDate->format('Y-m-d') . ' ' . $schedule->start_time);
        $endDateTime = Carbon::parse($scheduleDate->format('Y-m-d') . ' ' . $schedule->end_time);

        // Check if too early
        if ($now->lt($startDateTime)) {
            return view('applicant.exam_access', [
                'applicant' => $applicant,
                'assignedSchedule' => $assignedSchedule,
                'schedule' => $schedule,
                'exam' => $exam,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'status' => 'too_early',
                'message' => 'Your exam is not yet available. Please return at the scheduled start time.',
            ]);
        }

        // Check if too late
        if ($now->gt($endDateTime)) {
            return view('applicant.exam_access', [
                'applicant' => $applicant,
                'assignedSchedule' => $assignedSchedule,
                'schedule' => $schedule,
                'exam' => $exam,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'status' => 'too_late',
                'message' => 'Your exam schedule has expired. Please contact the Admission Office.',
            ]);
        }

        // Check if exam attempt already exists
        $examAttempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
            ->where('exam_id', $exam->exam_id)
            ->first();

        if ($examAttempt) {
            if ($examAttempt->finished_at) {
                // Already finished
                return view('applicant.exam_access', [
                    'applicant' => $applicant,
                    'assignedSchedule' => $assignedSchedule,
                    'schedule' => $schedule,
                    'exam' => $exam,
                    'examAttempt' => $examAttempt,
                    'startDateTime' => $startDateTime,
                    'endDateTime' => $endDateTime,
                    'status' => 'already_finished',
                    'message' => 'You have already completed this exam.',
                ]);
            } else {
                // In progress - redirect to exam taking page
                return redirect()->route('applicant.exam.take')
                    ->with('info', 'Resuming your exam...');
            }
        }

        // Everything is OK - allow starting exam
        return view('applicant.exam_access', [
            'applicant' => $applicant,
            'assignedSchedule' => $assignedSchedule,
            'schedule' => $schedule,
            'exam' => $exam,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'status' => 'ready',
            'message' => 'You are ready to start your exam.',
        ]);
    }

    /**
     * Start the exam by creating an exam attempt.
     */
    public function start(Request $request)
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Get the assigned schedule
        $assignedSchedule = $applicant->examSchedules()
            ->with(['examSchedule.exam'])
            ->latest()
            ->first();

        // Re-check all conditions for security
        if (!$assignedSchedule) {
            return redirect()->route('applicant.schedule')
                ->with('error', 'You have no exam schedule assigned.');
        }

        $schedule = $assignedSchedule->examSchedule;
        $exam = $schedule->exam;

        // Check time window - all times treated as Asia/Manila (app timezone)
        $now = Carbon::now();
        
        // Combine schedule_date with start_time and end_time
        // Use toDateString() to get YYYY-MM-DD without time component
        $examDate = $schedule->schedule_date->toDateString();
        $start = Carbon::parse($examDate . ' ' . $schedule->start_time);
        $end = Carbon::parse($examDate . ' ' . $schedule->end_time);
        
        // Temporary debug log
        \Log::debug("ExamAccessController@start - NOW=$now | START=$start | END=$end");

        if ($now->lt($start)) {
            return redirect()->route('applicant.schedule')
                ->with('error', 'The exam is not yet available.');
        }

        if ($now->gt($end)) {
            return redirect()->route('applicant.schedule')
                ->with('error', 'The exam schedule has expired.');
        }

        // Check if already has an attempt
        $existingAttempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
            ->where('exam_id', $exam->exam_id)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->finished_at) {
                return redirect()->route('applicant.schedule')
                    ->with('error', 'You have already completed this exam.');
            } else {
                // In progress - redirect to exam
                return redirect()->route('applicant.exam.take')
                    ->with('info', 'Resuming your exam...');
            }
        }

        // Create exam attempt
        $examAttempt = ExamAttempt::create([
            'exam_id' => $exam->exam_id,
            'applicant_id' => $applicant->applicant_id,
            'session_id' => Str::uuid()->toString(),
            'started_at' => now(),
        ]);

        // Redirect to exam taking page
        return redirect()->route('applicant.exam.take')
            ->with('success', 'Exam started. Good luck!');
    }
}

