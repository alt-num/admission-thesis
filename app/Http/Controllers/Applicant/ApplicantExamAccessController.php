<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
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
     * Check if the provided exam code is valid (AJAX endpoint).
     * Returns JSON: { valid: true/false }
     */
    public function checkCode(Request $request)
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Get the assigned schedule
        $assignedSchedule = $applicant->examSchedules()
            ->with(['examSchedule.exam'])
            ->latest()
            ->first();

        if (!$assignedSchedule) {
            return response()->json(['valid' => false], 200);
        }

        $schedule = $assignedSchedule->examSchedule;

        // Check if exam code is required
        $examCodeRequired = \App\Services\AntiCheatSettingsService::getFeature('exam_code_required', true);
        if (!$examCodeRequired || !$schedule->exam_code) {
            return response()->json(['valid' => true], 200);
        }

        $providedCode = strtoupper(trim($request->input('exam_code', '')));
        $storedCode = strtoupper(trim($schedule->exam_code));

        $isValid = ($providedCode === $storedCode);

        // Log invalid attempts
        if (!$isValid && $providedCode) {
            AntiCheatLog::create([
                'applicant_id' => $applicant->applicant_id,
                'event_type' => 'invalid_exam_code',
                'event_details' => [
                    'attempted_code' => $providedCode,
                    'schedule_id' => $schedule->schedule_id,
                    'timestamp' => now()->toISOString(),
                ],
                'event_timestamp' => now(),
            ]);
        }

        return response()->json(['valid' => $isValid], 200);
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

        // Check if exam code is required and validate
        $examCodeRequired = \App\Services\AntiCheatSettingsService::getFeature('exam_code_required', true);
        if ($examCodeRequired && $schedule->exam_code) {
            $providedCode = strtoupper(trim($request->exam_code));
            $storedCode = strtoupper(trim($schedule->exam_code));

            if ($providedCode !== $storedCode) {
                // Log invalid exam code attempt (no exam_attempt_id - exam hasn't started yet)
                AntiCheatLog::create([
                    'applicant_id' => $applicant->applicant_id,
                    'event_type' => 'invalid_exam_code',
                    'event_details' => [
                        'attempted_code' => $providedCode,
                        'schedule_id' => $schedule->schedule_id,
                        'timestamp' => now()->toISOString(),
                    ],
                    'event_timestamp' => now(),
                ]);

                // Silently redirect back - client-side validation should have caught this
                return redirect()->route('applicant.schedule');
            }
        }

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

        // Create exam attempt with IP address
        $examAttempt = ExamAttempt::create([
            'exam_id' => $exam->exam_id,
            'applicant_id' => $applicant->applicant_id,
            'session_id' => Str::uuid()->toString(),
            'started_at' => now(),
            'start_ip' => $request->ip(),
        ]);

        // Log successful exam code verification
        if ($schedule->exam_code) {
            AntiCheatLog::create([
                'applicant_id' => $applicant->applicant_id,
                'exam_attempt_id' => $examAttempt->attempt_id,
                'event_type' => 'exam_code_verified',
                'event_details' => [
                    'schedule_id' => $schedule->schedule_id,
                    'timestamp' => now()->toISOString(),
                ],
                'event_timestamp' => now(),
            ]);
        }

        // Redirect to exam taking page
        return redirect()->route('applicant.exam.take')
            ->with('success', 'Exam started. Good luck!');
    }
}

