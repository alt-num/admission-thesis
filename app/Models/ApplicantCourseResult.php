<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantCourseResult extends Model
{
    use HasFactory;

    protected $primaryKey = 'result_id';

    protected $table = 'applicant_course_results';

    protected $fillable = [
        'applicant_id',
        'course_id',
        'result_status',
        'score_value',
    ];

    protected $casts = [
        'score_value' => 'decimal:2',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
