<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSubsectionScore extends Model
{
    use HasFactory;

    protected $primaryKey = 'subsection_score_id';

    protected $guarded = [];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id', 'attempt_id');
    }

    public function subsection(): BelongsTo
    {
        return $this->belongsTo(ExamSubsection::class, 'subsection_id', 'subsection_id');
    }
}
