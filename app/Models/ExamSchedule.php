<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';

    protected $guarded = [];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function applicantExamSchedules(): HasMany
    {
        return $this->hasMany(ApplicantExamSchedule::class, 'schedule_id', 'schedule_id');
    }
}
