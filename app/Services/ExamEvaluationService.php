<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\ApplicantCourseResult;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class ExamEvaluationService
{
    /**
     * Evaluate an applicant's exam score and create course results.
     *
     * @param Applicant $applicant
     * @param float $scorePercentage The exam score percentage (0-100)
     * @return void
     */
    public static function evaluate(Applicant $applicant, float $scorePercentage): void
    {
        DB::transaction(function () use ($applicant, $scorePercentage) {
            // Check exam status first
            $examStatus = $applicant->examStatus();

            // Retrieve all preferred courses
            $preferredCourses = [
                $applicant->preferred_course_1,
                $applicant->preferred_course_2,
                $applicant->preferred_course_3,
            ];

            $hasQualified = false;

            // For each preferred course
            foreach ($preferredCourses as $courseId) {
                if (!$courseId) {
                    continue; // Skip if no course selected
                }

                $course = Course::find($courseId);
                if (!$course) {
                    continue; // Skip if course doesn't exist
                }

                // Determine result status based on exam status
                if ($examStatus === "Missed") {
                    $resultStatus = 'Missed';
                } else {
                    // Exam was completed, evaluate based on score
                    $passingScore = $course->passing_score;

                    // If passing_score is null, course does not evaluate score BUT attendance is still required
                    if ($passingScore === null) {
                        $resultStatus = 'Qualified';
                    } elseif ($scorePercentage >= $passingScore) {
                        $resultStatus = 'Qualified';
                    } else {
                        $resultStatus = 'NotQualified';
                    }
                }

                // Check if result already exists for this applicant and course
                $existingResult = ApplicantCourseResult::where('applicant_id', $applicant->applicant_id)
                    ->where('course_id', $courseId)
                    ->first();

                if ($existingResult) {
                    // Update existing result
                    $existingResult->update([
                        'result_status' => $resultStatus,
                        'score_value' => $examStatus === "Missed" ? 0 : $scorePercentage,
                    ]);
                } else {
                    // Create new result
                    ApplicantCourseResult::create([
                        'applicant_id' => $applicant->applicant_id,
                        'course_id' => $courseId,
                        'result_status' => $resultStatus,
                        'score_value' => $examStatus === "Missed" ? 0 : $scorePercentage,
                    ]);
                }

                // Track if any course qualified
                if ($resultStatus === 'Qualified') {
                    $hasQualified = true;
                }
            }

            // Update applicant status based on exam status and results
            if ($examStatus === "Missed") {
                // Missed exam = cannot qualify for ANY course
                $applicant->update([
                    'status' => 'NotQualified',
                ]);
            } elseif ($hasQualified) {
                // At least one course qualified
                $applicant->update([
                    'status' => 'Qualified',
                ]);
            } else {
                // Exam completed but no courses qualified
                $applicant->update([
                    'status' => 'NotQualified',
                ]);
            }
        });
    }
}

