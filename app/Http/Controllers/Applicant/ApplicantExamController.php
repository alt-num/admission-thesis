<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
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
            return redirect()->route('applicant.schedule')
                ->with('error', 'No active exam attempt found.');
        }

        // Check if already finished - redirect to results page
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

        return view('applicant.exam_take', compact(
            'exam',
            'attempt',
            'sections',
            'remainingSeconds',
            'existingAnswers',
            'schedule',
            'endDateTime'
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

        // Check if already finished
        if ($attempt->finished_at) {
            return response()->json(['success' => false, 'message' => 'Exam already finished'], 403);
        }

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
                return redirect()->route('applicant.exam.results')
                    ->with('info', 'Your exam has already been completed.');
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

        return redirect()->route('applicant.exam.results')
            ->with('success', 'Exam submitted successfully!');
    }

    /**
     * Calculate and save exam scores.
     */
    private function calculateScores(ExamAttempt $attempt)
    {
        // Get all answers for this attempt
        $answers = ExamAnswer::where('attempt_id', $attempt->attempt_id)
            ->with('question.subsection')
            ->get();

        // Calculate total score
        $totalCorrect = $answers->where('is_correct', true)->count();
        $totalQuestions = $answers->count();
        $scoreTotal = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;

        $attempt->score_total = round($scoreTotal, 2);

        // Calculate subsection scores
        $subsectionGroups = $answers->groupBy(function ($answer) {
            return $answer->question->subsection_id;
        });

        foreach ($subsectionGroups as $subsectionId => $subsectionAnswers) {
            $correct = $subsectionAnswers->where('is_correct', true)->count();
            $total = $subsectionAnswers->count();
            $score = $total > 0 ? ($correct / $total) * 100 : 0;

            ExamSubsectionScore::updateOrCreate(
                [
                    'attempt_id' => $attempt->attempt_id,
                    'subsection_id' => $subsectionId,
                ],
                [
                    'score' => round($score, 2),
                ]
            );
        }

        // Calculate verbal/nonverbal if subsections have types
        // This is a placeholder - implement based on your subsection types
        $attempt->score_verbal = $scoreTotal; // Placeholder
        $attempt->score_nonverbal = $scoreTotal; // Placeholder
    }
}

