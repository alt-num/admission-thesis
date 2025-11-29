<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicantCourseResult;
use App\Models\AntiCheatLog;
use App\Models\AntiCheatSetting;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamSubsectionScore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicantExamController extends Controller
{
    /**
     * Display the exam taking interface.
     */
    public function index()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Get the exam attempt
        $attempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
            ->whereNull('finished_at')
            ->with('exam')
            ->latest()
            ->first();

        if (!$attempt) {
            // Check if already finished
            $finishedAttempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
                ->whereNotNull('finished_at')
                ->latest()
                ->first();
            
            if ($finishedAttempt) {
                return redirect()->route('applicant.exam.results')
                    ->with('info', 'You have already completed this exam. View your results below.');
            }
            
            return redirect()->route('applicant.schedule')
                ->with('error', 'No active exam attempt found.');
        }

        // Double-check if already finished (race condition protection)
        if ($attempt->finished_at) {
            return redirect()->route('applicant.exam.results')
                ->with('info', 'You have already completed this exam. View your results below.');
        }

        // Get the assigned schedule
        $assignedSchedule = $applicant->examSchedules()
            ->with('examSchedule')
            ->where('schedule_id', '!=', null)
            ->latest()
            ->first();

        if (!$assignedSchedule) {
            return redirect()->route('applicant.schedule')
                ->with('error', 'No exam schedule found.');
        }

        $schedule = $assignedSchedule->examSchedule;
        
        // Check time window - all times treated as Asia/Manila (app timezone)
        $now = Carbon::now();
        
        // Combine schedule_date with start_time and end_time
        // Use toDateString() to get YYYY-MM-DD without time component
        $examDate = $schedule->schedule_date->toDateString();
        $startDateTime = Carbon::parse($examDate . ' ' . $schedule->start_time);
        $endDateTime = Carbon::parse($examDate . ' ' . $schedule->end_time);
        
        // Temporary debug log
        \Log::debug("ExamController@index - NOW=$now | START=$startDateTime | END=$endDateTime");

        if ($now->lt($startDateTime)) {
            return redirect()->route('applicant.schedule')
                ->with('error', 'The exam is not yet available.');
        }

        if ($now->gt($endDateTime)) {
            return redirect()->route('applicant.schedule')
                ->with('error', 'The exam schedule has expired.');
        }

        // Calculate remaining time in seconds (ensure integer)
        $remainingSeconds = (int) max(0, $now->diffInSeconds($endDateTime, false));

        // Get the exam with all nested data
        $exam = $attempt->exam;
        
        // Load all sections, subsections, questions, and choices with proper ordering
        $sections = $exam->sections()
            ->orderBy('order_no')
            ->with(['subsections' => function($query) {
                $query->orderBy('order_no')
                    ->with(['questions' => function($query) {
                        $query->orderBy('order_no')
                            ->with(['choices' => function($query) {
                                $query->orderBy('choice_id');
                            }]);
                    }]);
            }])
            ->get();

        // Get existing answers for this attempt
        $existingAnswers = ExamAnswer::where('attempt_id', $attempt->attempt_id)
            ->get()
            ->keyBy('question_id');

        // Check IP address consistency when loading exam page
        $this->checkIpAddress(request(), $attempt, $applicant);

        // Check if anti-cheat is enabled for this schedule
        $antiCheatEnabled = \App\Services\AntiCheatSettingsService::isEnabled();
        if ($antiCheatEnabled && isset($schedule->anti_cheat_enabled)) {
            $antiCheatEnabled = $schedule->anti_cheat_enabled;
        }

        // Get anti-cheat settings for banner display
        $settings = AntiCheatSetting::current();

        return view('applicant.exam_take', compact(
            'exam',
            'attempt',
            'sections',
            'remainingSeconds',
            'existingAnswers',
            'schedule',
            'endDateTime',
            'antiCheatEnabled',
            'settings'
        ));
    }

    /**
     * Save an answer via AJAX.
     */
    public function saveAnswer(Request $request)
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,attempt_id',
            'question_id' => 'required|exists:exam_questions,question_id',
            'choice_id' => 'nullable|exists:exam_choices,choice_id',
            'answer_value' => 'nullable|string',
        ]);

        // Get the attempt and verify it belongs to the authenticated applicant
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;
        
        $attempt = ExamAttempt::where('attempt_id', $validated['attempt_id'])
            ->where('applicant_id', $applicant->applicant_id)
            ->first();

        if (!$attempt) {
            return response()->json(['success' => false, 'message' => 'Invalid attempt'], 403);
        }

        // Check if already finished - STRICT enforcement
        if ($attempt->finished_at) {
            return response()->json([
                'success' => false, 
                'message' => 'Exam already finished. No further changes allowed.',
                'exam_finished' => true
            ], 403);
        }

        // Check IP address consistency
        $this->checkIpAddress($request, $attempt, $applicant);

        // Determine if answer is correct
        $isCorrect = false;
        if ($validated['choice_id']) {
            $choice = \App\Models\ExamChoice::find($validated['choice_id']);
            $isCorrect = $choice ? $choice->is_correct : false;
        }

        // Save or update the answer
        ExamAnswer::updateOrCreate(
            [
                'attempt_id' => $validated['attempt_id'],
                'question_id' => $validated['question_id'],
            ],
            [
                'choice_id' => $validated['choice_id'] ?? null,
                'answer_value' => $validated['answer_value'] ?? null,
                'is_correct' => $isCorrect,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Answer saved']);
    }

    /**
     * Finish the exam and calculate scores.
     */
    public function finishExam(Request $request)
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Get the active attempt - ensure it's not already finished
        $attempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
            ->whereNull('finished_at')
            ->lockForUpdate() // Prevent concurrent finalization
            ->first();

        if (!$attempt) {
            // Check if already finished
            $finishedAttempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
                ->whereNotNull('finished_at')
                ->latest()
                ->first();
            
            if ($finishedAttempt) {
                // Return JSON for AJAX requests, redirect for form submissions
                if ($request->wantsJson() || $request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Exam already completed',
                        'redirect' => route('applicant.exam.results')
                    ]);
                }
                
                return redirect()->route('applicant.exam.results')
                    ->with('info', 'Your exam has already been completed.');
            }
            
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active exam attempt found.'
                ], 404);
            }
            
            return redirect()->route('applicant.dashboard')
                ->with('error', 'No active exam attempt found.');
        }

        // Mark as finished (atomic operation)
        $attempt->finished_at = now();

        // Calculate scores
        $this->calculateScores($attempt);

        // Save attempt with all calculated scores
        $attempt->save();

        // Evaluate course results and update applicant status
        $this->evaluateCourseResultsAndUpdateStatus($applicant, $attempt);

        // Return JSON for AJAX requests, redirect for form submissions
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Exam submitted successfully',
                'redirect' => route('applicant.exam.results')
            ]);
        }

        return redirect()->route('applicant.exam.results')
            ->with('success', 'Exam submitted successfully!');
    }

    /**
     * Calculate and save exam scores.
     * Fully dynamic - based on exam structure, not just answered questions.
     */
    private function calculateScores(ExamAttempt $attempt)
    {
        // Load exam with all sections, subsections, and questions
        $exam = $attempt->exam()->with([
            'sections.subsections.questions' => function($query) {
                $query->orderBy('order_no');
            }
        ])->first();

        if (!$exam) {
            return;
        }

        // Get all answers for this attempt, keyed by question_id for quick lookup
        $answers = ExamAnswer::where('attempt_id', $attempt->attempt_id)
            ->get()
            ->keyBy('question_id');

        // Calculate subsection scores (dynamic - based on all questions in subsection)
        foreach ($exam->sections as $section) {
            foreach ($section->subsections as $subsection) {
                // Total questions in this subsection (from database structure)
                $totalQuestions = $subsection->questions->count();
                
                // Get question IDs for this subsection
                $questionIds = $subsection->questions->pluck('question_id')->toArray();
                
                // Count correct answers for questions in this subsection
                $correctAnswers = 0;
                foreach ($questionIds as $questionId) {
                    if (isset($answers[$questionId]) && $answers[$questionId]->is_correct) {
                        $correctAnswers++;
                    }
                }
                
                // Calculate percentage score
                $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

                // Save subsection score
                ExamSubsectionScore::updateOrCreate(
                    [
                        'attempt_id' => $attempt->attempt_id,
                        'subsection_id' => $subsection->subsection_id,
                    ],
                    [
                        'score' => round($score, 2),
                    ]
                );
            }
        }

        // Calculate overall exam score (dynamic - based on all questions in exam)
        $totalQuestions = 0;
        $totalCorrect = 0;
        
        foreach ($exam->sections as $section) {
            foreach ($section->subsections as $subsection) {
                $questionIds = $subsection->questions->pluck('question_id')->toArray();
                $totalQuestions += count($questionIds);
                
                foreach ($questionIds as $questionId) {
                    if (isset($answers[$questionId]) && $answers[$questionId]->is_correct) {
                        $totalCorrect++;
                    }
                }
            }
        }
        
        $scoreTotal = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;
        $attempt->score_total = round($scoreTotal, 2);

        // Calculate verbal/nonverbal if subsections have types
        // This is a placeholder - implement based on your subsection types
        $attempt->score_verbal = $scoreTotal; // Placeholder
        $attempt->score_nonverbal = $scoreTotal; // Placeholder
        
        $attempt->save();
    }

    /**
     * Evaluate course results and update applicant status.
     */
    private function evaluateCourseResultsAndUpdateStatus($applicant, ExamAttempt $attempt)
    {
        // Get preferred courses
        $preferredCourses = [
            1 => $applicant->preferredCourse1,
            2 => $applicant->preferredCourse2,
            3 => $applicant->preferredCourse3,
        ];

        $hasQualified = false;

        // Evaluate each preferred course
        foreach ($preferredCourses as $priority => $course) {
            if (!$course) {
                continue;
            }

            // Determine Qualified/NotQualified based on passing score
            // Database constraint requires 'Qualified' or 'NotQualified'
            // If passing_score is NULL, course only requires taking the exam (auto-qualify)
            if ($course->passing_score === null) {
                $resultStatus = 'Qualified';
                $passingScore = null; // No passing score requirement
            } else {
                $passingScore = $course->passing_score;
                $resultStatus = $attempt->score_total >= $passingScore ? 'Qualified' : 'NotQualified';
            }

            // Update or create course result (prevents duplicates via unique constraint)
            ApplicantCourseResult::updateOrCreate(
                [
                    'applicant_id' => $applicant->applicant_id,
                    'course_id' => $course->course_id,
                ],
                [
                    'result_status' => $resultStatus,
                    'score_value' => $attempt->score_total,
                ]
            );

            // Check if this course qualifies the applicant
            if ($resultStatus === 'Qualified') {
                $hasQualified = true;
            }
        }

        // Update applicant status based on qualification
        // If meets minimum for ANY course → Qualified
        // If meets NONE → NotQualified
        $newStatus = $hasQualified ? 'Qualified' : 'NotQualified';
        
        if ($applicant->status !== $newStatus) {
            $applicant->status = $newStatus;
            $applicant->save();
        }
    }

    /**
     * Check IP address consistency and log if changed.
     */
    private function checkIpAddress(Request $request, ExamAttempt $attempt, $applicant)
    {
        // Skip if IP logging is disabled
        if (!\App\Services\AntiCheatSettingsService::getFeature('ip_change_logging', true)) {
            return;
        }

        // Skip if anti-cheat is disabled
        if (!\App\Services\AntiCheatSettingsService::isEnabled()) {
            return;
        }

        // Skip if no start IP was recorded (for backward compatibility)
        if (!$attempt->start_ip) {
            return;
        }

        $currentIp = $request->ip();
        $startIp = $attempt->start_ip;

        // Check if IP has changed
        if ($currentIp !== $startIp) {
            // Mark attempt as having IP inconsistency
            if (!$attempt->ip_changed) {
                $attempt->ip_changed = true;
                $attempt->save();
            }

            // Log IP change event based on strictness setting
            $strictness = \App\Services\AntiCheatSettingsService::getIpCheckStrictness();

            if ($strictness === 'log_only') {
                // Log the IP change event
                AntiCheatLog::create([
                    'applicant_id' => $applicant->applicant_id,
                    'exam_attempt_id' => $attempt->attempt_id,
                    'event_type' => 'ip_changed',
                    'event_details' => [
                        'start_ip' => $startIp,
                        'current_ip' => $currentIp,
                        'timestamp' => now()->toISOString(),
                    ],
                    'event_timestamp' => now(),
                ]);
            }
            // Future: Handle 'warn' and 'block' strictness modes
        }
    }
}

