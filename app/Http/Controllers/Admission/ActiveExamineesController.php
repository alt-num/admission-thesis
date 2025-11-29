<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActiveExamineesController extends Controller
{
    /**
     * Display the active examinees monitor page.
     */
    public function index()
    {
        $activeExaminees = $this->getActiveExaminees();
        
        return view('admission.active_examinees.index', compact('activeExaminees'));
    }

    /**
     * Fetch active examinees data (for AJAX refresh).
     */
    public function fetch()
    {
        $activeExaminees = $this->getActiveExaminees();
        
        // Convert dates to ISO strings for JSON
        $examinees = array_map(function($examinee) {
            $examinee['started_at'] = $examinee['started_at']->toIso8601String();
            if ($examinee['last_activity']) {
                $examinee['last_activity'] = $examinee['last_activity']->toIso8601String();
            }
            return $examinee;
        }, $activeExaminees);
        
        return response()->json($examinees);
    }

    /**
     * Get all active examinees with their violation counts and status.
     */
    private function getActiveExaminees()
    {
        $idleMinutes = \App\Services\AntiCheatSettingsService::getIdleTimeoutMinutes();
        
        // Get all active exam attempts (started but not finished)
        $activeAttempts = ExamAttempt::whereNotNull('started_at')
            ->whereNull('finished_at')
            ->with(['applicant', 'exam'])
            ->get();

        $examinees = [];

        foreach ($activeAttempts as $attempt) {
            // Get total event count (informational only)
            $eventCount = AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
                ->count();

            // Get suspicious event count (informational only)
            $suspiciousCount = AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
                ->whereIn('event_type', ['tab_switch', 'window_blur', 'window_hidden', 'visibility_hidden', 'refresh'])
                ->count();

            // Check for IP change
            $hadIpChange = $attempt->ip_changed ?? false;
            
            // Check if exam code was verified
            $examCodeVerified = AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
                ->where('event_type', 'exam_code_verified')
                ->exists();

            // Get current window/tab state from most recent log
            $lastLog = AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
                ->latest('event_timestamp')
                ->first();
            
            $currentState = 'Unknown';
            if ($lastLog) {
                $eventType = $lastLog->event_type;
                if (in_array($eventType, ['window_blur', 'window_hidden', 'visibility_hidden', 'tab_switch'])) {
                    $currentState = 'Hidden/Blurred';
                } elseif ($eventType === 'window_focus' || $eventType === 'visibility_return') {
                    $currentState = 'Focused';
                } else {
                    $currentState = 'Active';
                }
            }

            // Get last activity timestamp (most recent answer save or anti-cheat log)
            $lastAnswer = ExamAnswer::where('attempt_id', $attempt->attempt_id)
                ->latest('updated_at')
                ->first();
            
            $lastLog = AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
                ->latest('event_timestamp')
                ->first();

            $lastActivity = null;
            if ($lastAnswer && $lastLog) {
                $lastActivity = $lastAnswer->updated_at->gt($lastLog->event_timestamp) 
                    ? $lastAnswer->updated_at 
                    : $lastLog->event_timestamp;
            } elseif ($lastAnswer) {
                $lastActivity = $lastAnswer->updated_at;
            } elseif ($lastLog) {
                $lastActivity = $lastLog->event_timestamp;
            } else {
                $lastActivity = $attempt->started_at;
            }

            // Determine status (idle is status-only, informational)
            $status = 'Active';
            if ($lastActivity) {
                $minutesSinceActivity = Carbon::now()->diffInMinutes($lastActivity);
                if ($minutesSinceActivity >= $idleMinutes) {
                    $status = 'Idle'; // Status-only, informational only
                }
            }
            
            if ($attempt->finished_at) {
                $status = 'Finished';
            }

            // Get schedule info (if available)
            $scheduleInfo = null;
            $applicantSchedule = $attempt->applicant->examSchedules()
                ->with('examSchedule')
                ->whereHas('examSchedule', function($query) use ($attempt) {
                    $query->where('exam_id', $attempt->exam_id);
                })
                ->latest()
                ->first();

            if ($applicantSchedule && $applicantSchedule->examSchedule) {
                $schedule = $applicantSchedule->examSchedule;
                $scheduleInfo = [
                    'date' => $schedule->schedule_date->format('M d, Y'),
                    'time' => date('g:i A', strtotime($schedule->start_time)) . ' - ' . date('g:i A', strtotime($schedule->end_time)),
                    'exam_code' => $schedule->exam_code ?? null,
                ];
            }

            $examinees[] = [
                'attempt_id' => $attempt->attempt_id,
                'applicant_id' => $attempt->applicant_id,
                'applicant_name' => $attempt->applicant->first_name . ' ' . $attempt->applicant->last_name,
                'app_ref_no' => $attempt->applicant->app_ref_no,
                'exam_name' => $attempt->exam->title ?? 'N/A',
                'schedule_info' => $scheduleInfo,
                'started_at' => $attempt->started_at,
                'last_activity' => $lastActivity,
                'event_count' => $eventCount,
                'suspicious_count' => $suspiciousCount,
                'current_state' => $currentState,
                'had_ip_change' => $hadIpChange,
                'exam_code_verified' => $examCodeVerified,
                'status' => $status,
            ];
        }

        // Sort by started_at descending (most recent first)
        usort($examinees, function($a, $b) {
            return $b['started_at']->lt($a['started_at']) ? -1 : 1;
        });

        return $examinees;
    }
}
