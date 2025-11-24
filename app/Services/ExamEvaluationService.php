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
            // Retrieve all preferred courses
            $preferredCourses = [
                $applicant->preferred_course_1,
                $applicant->preferred_course_2,
                $applicant->preferred_course_3,
            ];

            // For each preferred course
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

                // Determine result status
                // If passing_score is null → result = 'Pass' (no minimum requirement)
                if ($passingScore === null) {
                    $resultStatus = 'Pass';
                } elseif ($scorePercentage >= $passingScore) {
                    $resultStatus = 'Pass';
                } else {
                    $resultStatus = 'Fail';
                }

                // Check if result already exists for this applicant and course
                $existingResult = ApplicantCourseResult::where('applicant_id', $applicant->applicant_id)
                    ->where('course_id', $courseId)
                    ->first();

                if ($existingResult) {
                    // Update existing result
                    $existingResult->update([
                        'result_status' => $resultStatus,
                        'score_value' => $scorePercentage,
                    ]);
                } else {
                    // Create new result
                    ApplicantCourseResult::create([
                        'applicant_id' => $applicant->applicant_id,
                        'course_id' => $courseId,
                        'result_status' => $resultStatus,
                        'score_value' => $scorePercentage,
                    ]);
                }
            }

            // Update applicant status to 'ExamTaken'
            $applicant->update([
                'status' => 'ExamTaken',
            ]);
        });
    }
}

