<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantExamSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    public function examSchedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class, 'schedule_id', 'schedule_id');
    }
}
