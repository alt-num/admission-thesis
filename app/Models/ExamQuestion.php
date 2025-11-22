<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'question_id';

    protected $guarded = [];

    public function subsection(): BelongsTo
    {
        return $this->belongsTo(ExamSubsection::class, 'subsection_id', 'subsection_id');
    }

    public function choices(): HasMany
    {
        return $this->hasMany(ExamChoice::class, 'question_id', 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class, 'question_id', 'question_id');
    }
}
