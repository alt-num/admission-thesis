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

        // Get answers keyed by question_id for quick lookup
        $answersByQuestionId = $answers->keyBy('question_id');

        // Calculate subsection scores (dynamic - based on all questions in subsection)
        $subsectionScoresData = [];
        foreach ($sections as $section) {
            foreach ($section->subsections as $subsection) {
                // Total questions in this subsection (from database structure)
                $totalQuestions = $subsection->questions->count();
                
                // Get question IDs for this subsection
                $questionIds = $subsection->questions->pluck('question_id')->toArray();
                
                // Count correct answers for questions in this subsection
                $correctAnswers = 0;
                foreach ($questionIds as $questionId) {
                    if (isset($answersByQuestionId[$questionId]) && $answersByQuestionId[$questionId]->is_correct) {
                        $correctAnswers++;
                    }
                }
                
                $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
                
                $subsectionScoresData[$subsection->subsection_id] = [
                    'subsection' => $subsection,
                    'correct' => $correctAnswers,
                    'total' => $totalQuestions,
                    'percentage' => $percentage,
                ];
            }
        }

        // Calculate section scores (dynamic - sum of all subsections in section)
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

        // Calculate overall score (dynamic - based on all questions in exam)
        $overallCorrect = 0;
        $overallTotal = 0;
        
        foreach ($sections as $section) {
            foreach ($section->subsections as $subsection) {
                $questionIds = $subsection->questions->pluck('question_id')->toArray();
                $overallTotal += count($questionIds);
                
                foreach ($questionIds as $questionId) {
                    if (isset($answersByQuestionId[$questionId]) && $answersByQuestionId[$questionId]->is_correct) {
                        $overallCorrect++;
                    }
                }
            }
        }
        
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
        $examStatus = $applicant->examStatus();

        foreach ($preferredCourses as $priority => $course) {
            if (!$course) {
                continue;
            }

            // Determine result status based on exam status
            if ($examStatus === "Missed") {
                $resultStatus = 'Missed';
                $passingScore = null;
            } else {
                // Exam was completed, evaluate based on score
                // If passing_score is NULL, course does not evaluate score BUT attendance is still required
                if ($course->passing_score === null) {
                    $resultStatus = 'Qualified';
                    $passingScore = null; // No passing score requirement
                } else {
                    $passingScore = $course->passing_score;
                    $resultStatus = $attempt->score_total >= $passingScore ? 'Qualified' : 'NotQualified';
                }
            }

            // Update or create course result (prevents duplicates via unique constraint)
            $courseResult = ApplicantCourseResult::updateOrCreate(
                [
                    'applicant_id' => $applicant->applicant_id,
                    'course_id' => $course->course_id,
                ],
                [
                    'result_status' => $resultStatus,
                    'score_value' => $examStatus === "Missed" ? 0 : $attempt->score_total,
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
        $examStatus = $applicant->examStatus();

        // If exam was missed, applicant cannot qualify for ANY course
        if ($examStatus === "Missed") {
            $newStatus = 'NotQualified';
        } else {
            // Check if at least one course qualified
            $hasQualified = false;
            foreach ($courseResults as $result) {
                if ($result['status'] === 'Qualified') {
                    $hasQualified = true;
                    break;
                }
            }

            // Update applicant status
            $newStatus = $hasQualified ? 'Qualified' : 'NotQualified';
        }
        
        if ($applicant->status !== $newStatus) {
            $applicant->status = $newStatus;
            $applicant->save();
        }
    }
}

