<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $primaryKey = 'answer_id';

    protected $guarded = [];

    protected $casts = [
        'answer_value' => 'boolean',
        'is_correct' => 'boolean',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id', 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id', 'question_id');
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(ExamChoice::class, 'choice_id', 'choice_id');
    }
}
