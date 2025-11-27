<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicantCourseResult;
use App\Models\Course;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamSubsectionScore;
use Illuminate\Http\Request;

class ApplicantExamResultController extends Controller
{
    /**
     * Display the exam results page.
     */
    public function index()
    {
        $applicantUser = auth()->guard('applicant')->user();
        $applicant = $applicantUser->applicant;

        // Get the latest finished exam attempt
        $attempt = ExamAttempt::where('applicant_id', $applicant->applicant_id)
            ->whereNotNull('finished_at')
            ->with(['exam'])
            ->latest()
            ->first();

        if (!$attempt) {
            return redirect()->route('applicant.dashboard')
                ->with('error', 'No completed exam found.');
        }

        // Load all sections for this exam with proper ordering and questions
        $sections = $attempt->exam->sections()
            ->orderBy('order_no')
            ->with(['subsections' => function($query) {
                $query->orderBy('order_no')
                    ->with(['questions' => function($query) {
                        $query->orderBy('order_no');
                    }]);
            }])
            ->get();

        // Get all answers for this attempt with question->subsection->section relationships
        $answers = ExamAnswer::where('attempt_id', $attempt->attempt_id)
            ->with('question.subsection.section')
            ->get();

        // Calculate subsection scores (correct/total/percentage)
        $subsectionScoresData = [];
        foreach ($sections as $section) {
            foreach ($section->subsections as $subsection) {
                // Get answers for questions in this subsection
                $subsectionAnswers = $answers->filter(function($answer) use ($subsection) {
                    return $answer->question->subsection_id === $subsection->subsection_id;
                });
                
                $correct = $subsectionAnswers->where('is_correct', true)->count();
                $total = $subsectionAnswers->count();
                $percentage = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
                
                $subsectionScoresData[$subsection->subsection_id] = [
                    'subsection' => $subsection,
                    'correct' => $correct,
                    'total' => $total,
                    'percentage' => $percentage,
                ];
            }
        }

        // Calculate section scores (correct/total/percentage)
        $sectionScoresData = [];
        foreach ($sections as $section) {
            $sectionCorrect = 0;
            $sectionTotal = 0;
            
            foreach ($section->subsections as $subsection) {
                if (isset($subsectionScoresData[$subsection->subsection_id])) {
                    $sectionCorrect += $subsectionScoresData[$subsection->subsection_id]['correct'];
                    $sectionTotal += $subsectionScoresData[$subsection->subsection_id]['total'];
                }
            }
            
            $sectionPercentage = $sectionTotal > 0 ? round(($sectionCorrect / $sectionTotal) * 100, 2) : 0;
            
            $sectionScoresData[$section->section_id] = [
                'section' => $section,
                'correct' => $sectionCorrect,
                'total' => $sectionTotal,
                'percentage' => $sectionPercentage,
            ];
        }

        // Calculate overall score (correct/total/percentage)
        $overallCorrect = $answers->where('is_correct', true)->count();
        $overallTotal = $answers->count();
        $overallPercentage = $overallTotal > 0 ? round(($overallCorrect / $overallTotal) * 100, 2) : 0;

        // Get preferred courses
        $preferredCourses = [
            1 => $applicant->preferredCourse1,
            2 => $applicant->preferredCourse2,
            3 => $applicant->preferredCourse3,
        ];

        // Compute or retrieve course results
        $courseResults = $this->computeCourseResults($applicant, $attempt, $preferredCourses);

        // Update applicant status based on results
        $this->updateApplicantStatus($applicant, $courseResults);

        return view('applicant.exam_results', compact(
            'applicant',
            'attempt',
            'sections',
            'subsectionScoresData',
            'sectionScoresData',
            'overallCorrect',
            'overallTotal',
            'overallPercentage',
            'preferredCourses',
            'courseResults'
        ));
    }

    /**
     * Compute PASS/FAIL for each preferred course.
     */
    private function computeCourseResults($applicant, $attempt, $preferredCourses)
    {
        $results = [];

        foreach ($preferredCourses as $priority => $course) {
            if (!$course) {
                continue;
            }

            // Determine PASS/FAIL based on passing score
            // Database constraint requires 'Pass' or 'Fail' (not 'PASS' or 'FAIL')
            // If passing_score is NULL, course only requires taking the exam (auto-pass)
            if ($course->passing_score === null) {
                $resultStatus = 'Pass';
                $passingScore = null; // No passing score requirement
            } else {
                $passingScore = $course->passing_score;
                $resultStatus = $attempt->score_total >= $passingScore ? 'Pass' : 'Fail';
            }

            // Update or create course result (prevents duplicates via unique constraint)
            $courseResult = ApplicantCourseResult::updateOrCreate(
                [
                    'applicant_id' => $applicant->applicant_id,
                    'course_id' => $course->course_id,
                ],
                [
                    'result_status' => $resultStatus,
                    'score_value' => $attempt->score_total,
                ]
            );

            $results[$priority] = [
                'course' => $course,
                'result' => $courseResult,
                'status' => $resultStatus,
                'passing_score' => $passingScore,
            ];
        }

        return $results;
    }

    /**
     * Update applicant status based on course evaluation results.
     */
    private function updateApplicantStatus($applicant, $courseResults)
    {
        // Check if at least one course passed
        $hasPassed = false;
        foreach ($courseResults as $result) {
            if ($result['status'] === 'Pass') {
                $hasPassed = true;
                break;
            }
        }

        // Update applicant status
        $newStatus = $hasPassed ? 'Passed' : 'Failed';
        
        if ($applicant->status !== $newStatus) {
            $applicant->status = $newStatus;
            $applicant->save();
        }
    }
}

