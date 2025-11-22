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
}
