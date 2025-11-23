<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\ApplicantCourseResult;
use App\Models\Course;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\DB;

class ExamEvaluationService
{
    /**
     * Evaluate an applicant's exam attempt and create course results.
     *
     * @param ExamAttempt $examAttempt
     * @return void
     */
    public function evaluateExamAttempt(ExamAttempt $examAttempt): void
    {
        DB::transaction(function () use ($examAttempt) {
            $applicant = $examAttempt->applicant;
            
            // Get the total score from the exam attempt
            // Assuming score_total is already a percentage or total score
            $applicantScore = (float) $examAttempt->score_total;

            // Evaluate each preferred course
            $preferredCourses = [
                $applicant->preferred_course_1,
                $applicant->preferred_course_2,
                $applicant->preferred_course_3,
            ];

            foreach ($preferredCourses as $courseId) {
                if (!$courseId) {
                    continue; // Skip if no course selected
                }

                $course = Course::find($courseId);
                if (!$course) {
                    continue; // Skip if course doesn't exist
                }

                // Get passing score for the course
                $passingScore = $course->passing_score;

                // If passing_score is null, skip evaluation for this course
                if ($passingScore === null) {
                    continue;
                }

                // Determine result status
                $resultStatus = $applicantScore >= $passingScore ? 'Pass' : 'Fail';

                // Check if result already exists for this applicant and course
                $existingResult = ApplicantCourseResult::where('applicant_id', $applicant->applicant_id)
                    ->where('course_id', $courseId)
                    ->first();

                if ($existingResult) {
                    // Update existing result
                    $existingResult->update([
                        'result_status' => $resultStatus,
                        'score_value' => $applicantScore,
                    ]);
                } else {
                    // Create new result
                    ApplicantCourseResult::create([
                        'applicant_id' => $applicant->applicant_id,
                        'course_id' => $courseId,
                        'result_status' => $resultStatus,
                        'score_value' => $applicantScore,
                    ]);
                }
            }

            // Update applicant status to 'ExamTaken'
            $applicant->update([
                'status' => 'ExamTaken',
            ]);
        });
    }

    /**
     * Re-evaluate an applicant's exam results (useful if passing scores change).
     *
     * @param Applicant $applicant
     * @return void
     */
    public function reevaluateApplicant(Applicant $applicant): void
    {
        // Get the most recent completed exam attempt
        $latestAttempt = $applicant->examAttempts()
            ->whereNotNull('finished_at')
            ->orderBy('finished_at', 'desc')
            ->first();

        if ($latestAttempt) {
            $this->evaluateExamAttempt($latestAttempt);
        }
    }
}

