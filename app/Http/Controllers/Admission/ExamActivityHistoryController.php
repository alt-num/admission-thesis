<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ExamActivityHistoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->checkAdminAccess();
    }

    /**
     * Check if the current user is an admin.
     */
    private function checkAdminAccess()
    {
        $user = auth()->guard('admission')->user();
        if (!$user || $user->role !== 'Admin') {
            abort(403, 'Only administrators can access exam activity history.');
        }
    }

    /**
     * Display the exam activity history page.
     */
    public function index(Request $request)
    {
        $query = ExamAttempt::with(['applicant', 'exam'])
            ->whereNotNull('started_at');

        // Filter by exam
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        // Filter by schedule (NEW)
        if ($request->filled('schedule_id')) {
            // Get all applicant_ids assigned to this schedule
            $applicantIds = \App\Models\ApplicantExamSchedule::where('schedule_id', $request->schedule_id)
                ->pluck('applicant_id');
            
            // Also get the exam_id from the schedule to ensure we're filtering correctly
            $schedule = \App\Models\ExamSchedule::find($request->schedule_id);
            if ($schedule) {
                $query->where('exam_id', $schedule->exam_id)
                      ->whereIn('applicant_id', $applicantIds);
            }
        }

        // Filter by applicant (NEW - searchable)
        if ($request->filled('applicant_id')) {
            $query->where('applicant_id', $request->applicant_id);
        } elseif ($request->filled('applicant_search')) {
            // Search by name or app_ref_no
            $search = $request->applicant_search;
            $query->whereHas('applicant', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('app_ref_no', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        // Filter by event type (NEW) - filter attempts that have logs with these event types
        if ($request->filled('event_types')) {
            $eventTypes = is_array($request->event_types) ? $request->event_types : [$request->event_types];
            $attemptIds = AntiCheatLog::whereIn('event_type', $eventTypes)
                ->whereNotNull('exam_attempt_id')
                ->pluck('exam_attempt_id')
                ->unique();
            $query->whereIn('attempt_id', $attemptIds);
        }

        // Suspicious only toggle (NEW)
        if ($request->boolean('suspicious_only')) {
            $suspiciousTypes = ['tab_switch', 'window_blur', 'window_hidden', 'visibility_hidden', 'refresh'];
            $attemptIds = AntiCheatLog::whereIn('event_type', $suspiciousTypes)
                ->whereNotNull('exam_attempt_id')
                ->pluck('exam_attempt_id')
                ->unique();
            $query->whereIn('attempt_id', $attemptIds);
        }

        // Sorting (NEW)
        $sortOrder = $request->get('sort', 'desc'); // 'desc' = newest first (default), 'asc' = oldest first
        $query->orderBy('started_at', $sortOrder);

        $attempts = $query->paginate(25)->withQueryString();

        // Get all exams for filter dropdown
        $exams = \App\Models\Exam::orderBy('title')->get();

        // Get all schedules for selected exam (NEW)
        $schedules = collect();
        if ($request->filled('exam_id')) {
            $schedules = \App\Models\ExamSchedule::where('exam_id', $request->exam_id)
                ->orderBy('schedule_date')
                ->orderBy('start_time')
                ->get();
        }

        // Get all applicants for search (NEW - limit to recent for performance)
        $applicants = \App\Models\Applicant::orderBy('last_name')
            ->orderBy('first_name')
            ->limit(1000)
            ->get();

        return view('admission.exam_activity_history.index', compact('attempts', 'exams', 'schedules', 'applicants'));
    }

    /**
     * Show activity log for a specific exam attempt.
     */
    public function show(ExamAttempt $attempt)
    {
        $this->checkAdminAccess();
        
        // Ensure attempt exists
        if (!$attempt) {
            abort(404);
        }

        // Load relationships
        $attempt->load(['applicant', 'exam']);

        // Get all anti-cheat logs for this attempt
        $logs = AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
            ->orderBy('event_timestamp', 'desc')
            ->get();

        // Get event count (informational only - no punishment)
        $eventCount = $logs->count();

        // Check for suspicious events (informational only)
        $suspiciousEvents = $logs->whereIn('event_type', ['tab_switch', 'window_blur', 'window_hidden', 'visibility_hidden', 'refresh'])->count();

        // Check for exam code verification
        $examCodeVerified = $logs->where('event_type', 'exam_code_verified')->isNotEmpty();

        return view('admission.exam_activity_history.show', compact('attempt', 'logs', 'eventCount', 'suspiciousEvents', 'examCodeVerified'));
    }
}
