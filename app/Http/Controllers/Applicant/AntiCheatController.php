<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AntiCheatController extends Controller
{
    /**
     * Log an anti-cheat event from the frontend.
     */
    public function logEvent(Request $request)
    {
        // Check if anti-cheat is enabled
        if (!\App\Services\AntiCheatSettingsService::isEnabled()) {
            return response()->json(['success' => true, 'message' => 'Anti-cheat disabled']);
        }

        $validated = $request->validate([
            'exam_attempt_id' => 'required|exists:exam_attempts,attempt_id',
            'event_type' => 'required|string|max:100',
            'event_details' => 'nullable|array',
        ]);

        // Get the authenticated applicant
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Verify the attempt belongs to this applicant
        $attempt = ExamAttempt::where('attempt_id', $validated['exam_attempt_id'])
            ->where('applicant_id', $applicant->applicant_id)
            ->first();

        if (!$attempt) {
            return response()->json(['success' => false, 'message' => 'Invalid attempt'], 403);
        }

        // Create the log entry
        AntiCheatLog::create([
            'applicant_id' => $applicant->applicant_id,
            'exam_attempt_id' => $validated['exam_attempt_id'],
            'event_type' => $validated['event_type'],
            'event_details' => $validated['event_details'] ?? [],
            'event_timestamp' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Event logged']);
    }
}
