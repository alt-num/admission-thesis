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
