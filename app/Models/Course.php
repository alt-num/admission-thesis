<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $primaryKey = 'course_id';

    protected $fillable = [
        'course_code',
        'course_name',
        'department_id',
        'passing_score',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function applicantsPrimaryPreference(): HasMany
    {
        return $this->hasMany(Applicant::class, 'preferred_course_1', 'course_id');
    }

    public function applicantsSecondaryPreference(): HasMany
    {
        return $this->hasMany(Applicant::class, 'preferred_course_2', 'course_id');
    }

    public function applicantsTertiaryPreference(): HasMany
    {
        return $this->hasMany(Applicant::class, 'preferred_course_3', 'course_id');
    }

    public function courseResults(): HasMany
    {
        return $this->hasMany(ApplicantCourseResult::class, 'course_id', 'course_id');
    }
}
