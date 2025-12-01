<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Applicant extends Model
{
    use HasFactory;

    protected $primaryKey = 'applicant_id';

    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id', 'campus_id');
    }

    public function preferredCourse1(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'preferred_course_1', 'course_id');
    }

    public function preferredCourse2(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'preferred_course_2', 'course_id');
    }

    public function preferredCourse3(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'preferred_course_3', 'course_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(AdmissionUser::class, 'registered_by', 'admission_user_id');
    }

    public function applicantUser(): HasOne
    {
        return $this->hasOne(ApplicantUser::class, 'applicant_id', 'applicant_id');
    }

    public function declaration(): HasOne
    {
        return $this->hasOne(ApplicantDeclaration::class, 'applicant_id', 'applicant_id');
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'applicant_id', 'applicant_id');
    }

    public function courseResults(): HasMany
    {
        return $this->hasMany(ApplicantCourseResult::class, 'applicant_id', 'applicant_id');
    }

    public function examSchedules(): HasMany
    {
        return $this->hasMany(ApplicantExamSchedule::class, 'applicant_id', 'applicant_id');
    }

    public function antiCheatLogs(): HasMany
    {
        return $this->hasMany(AntiCheatLog::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Get the exam status for this applicant.
     * Returns: "Completed", "Missed", or "NotStarted"
     */
    public function examStatus(): string
    {
        // Has exam attempt?
        if ($this->examAttempts()->exists()) {
            return "Completed";
        }

        // Get the latest exam schedule
        $applicantExamSchedule = $this->examSchedules()
            ->with('examSchedule')
            ->latest()
            ->first();

        if (!$applicantExamSchedule || !$applicantExamSchedule->examSchedule) {
            return "NotStarted";
        }

        $schedule = $applicantExamSchedule->examSchedule;

        // Combine schedule_date and end_time to get full datetime
        $endDateTime = \Carbon\Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->end_time);

        // Missed exam if time passed and no attempt
        if (now()->greaterThan($endDateTime)) {
            return "Missed";
        }

        return "NotStarted";
    }

    /**
     * Automatically evaluate and update course results and applicant status if needed.
     * This method can be safely called on any page load and only performs DB updates if changes are needed.
     */
    public function evaluateResultsIfNeeded(): void
    {
        $status = $this->examStatus();

        if ($status === "Completed") {
            // Normal evaluation: check if we have an exam attempt with score
            $attempt = $this->examAttempts()->latest()->first();
            
            if (!$attempt || !$attempt->finished_at) {
                // Exam not finished yet, don't evaluate
                return;
            }

            // Get preferred courses
            $preferredCourses = [
                $this->preferred_course_1,
                $this->preferred_course_2,
                $this->preferred_course_3,
            ];

            $hasQualified = false;
            $scoreTotal = $attempt->score_total ?? 0;

            // Evaluate each preferred course
            foreach ($preferredCourses as $courseId) {
                if (!$courseId) {
                    continue;
                }

                $course = \App\Models\Course::find($courseId);
                if (!$course) {
                    continue;
                }

                // Determine result status based on score
                if ($course->passing_score === null) {
                    $resultStatus = 'Qualified';
                } elseif ($scoreTotal >= $course->passing_score) {
                    $resultStatus = 'Qualified';
                } else {
                    $resultStatus = 'NotQualified';
                }

                // Update or create course result
                $courseResult = \App\Models\ApplicantCourseResult::updateOrCreate(
                    [
                        'applicant_id' => $this->applicant_id,
                        'course_id' => $courseId,
                    ],
                    [
                        'result_status' => $resultStatus,
                        'score_value' => $scoreTotal,
                    ]
                );

                if ($resultStatus === 'Qualified') {
                    $hasQualified = true;
                }
            }

            // Update applicant status
            $newStatus = $hasQualified ? 'Qualified' : 'NotQualified';
            if ($this->status !== $newStatus) {
                $this->status = $newStatus;
                $this->save();
            }

            return;
        }

        if ($status === "Missed") {
            // FORCE all course results to "Missed"
            foreach ($this->courseResults as $result) {
                if ($result->result_status !== "Missed") {
                    $result->result_status = "Missed";
                    $result->save();
                }
            }

            // Also ensure preferred courses have results marked as "Missed"
            $preferredCourses = [
                $this->preferred_course_1,
                $this->preferred_course_2,
                $this->preferred_course_3,
            ];

            foreach ($preferredCourses as $courseId) {
                if (!$courseId) {
                    continue;
                }

                \App\Models\ApplicantCourseResult::updateOrCreate(
                    [
                        'applicant_id' => $this->applicant_id,
                        'course_id' => $courseId,
                    ],
                    [
                        'result_status' => 'Missed',
                        'score_value' => 0,
                    ]
                );
            }

            // Applicant missed exam → cannot qualify for ANY course
            if ($this->status !== "NotQualified") {
                $this->status = "NotQualified";
                $this->save();
            }

            return;
        }

        // If NotStarted → do NOT evaluate
    }

    /**
     * Check if the applicant's profile is complete.
     * Profile is considered complete if all required fields are filled and the applicant has logged in at least once.
     *
     * @return bool
     */
    public function isProfileComplete(): bool
    {
        // Check if applicant has logged in (has applicantUser and has completed profile)
        if (!$this->applicantUser) {
            return false;
        }

        // Check if all required profile fields are filled
        $requiredFields = [
            'first_name',
            'last_name',
            'birth_date',
            'place_of_birth',
            'sex',
            'civil_status',
            'email',
            'contact_number',
            'barangay',
            'municipality',
            'province',
            'last_school_attended',
            'school_address',
            'year_graduated',
            'gen_average',
            'preferred_course_1',
            'preferred_course_2',
            'preferred_course_3',
            'photo_path',
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Check if declaration exists and is complete
        if (!$this->declaration) {
            return false;
        }

        $requiredDeclarationFields = [
            'physical_condition_flag',
            'disciplinary_action_flag',
            'certified_signature_name',
            'certified_date',
        ];

        foreach ($requiredDeclarationFields as $field) {
            if (!isset($this->declaration->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate a unique application reference number.
     * Format: <city_code>-<year><sequence>
     * Example: BOR-2513800, GUI-2500001
     *
     * @param Campus $campus
     * @return string
     */
    public static function generateRefNumber(Campus $campus): string
    {
        $cityCode = $campus->city_code;
        $year = date('y'); // Last 2 digits of current year

        // Find the highest sequence number for this prefix
        $prefix = "{$cityCode}-{$year}";
        $existingRefs = self::where('app_ref_no', 'like', "{$prefix}%")
            ->orderBy('app_ref_no', 'desc')
            ->pluck('app_ref_no');

        $nextSequence = 1;

        if ($existingRefs->isNotEmpty()) {
            // Extract the sequence number from the last ref
            $lastRef = $existingRefs->first();
            // Remove prefix and get the sequence part
            $lastSequence = (int) substr($lastRef, strlen($prefix));
            $nextSequence = $lastSequence + 1;
        }

        // Zero-pad to at least 5 digits, but allow higher numbers
        $sequence = str_pad($nextSequence, 5, '0', STR_PAD_LEFT);

        $appRefNo = "{$prefix}{$sequence}";

        // Ensure uniqueness (in case of race condition)
        while (self::where('app_ref_no', $appRefNo)->exists()) {
            $nextSequence++;
            $sequence = str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
            $appRefNo = "{$prefix}{$sequence}";
        }

        return $appRefNo;
    }
}
