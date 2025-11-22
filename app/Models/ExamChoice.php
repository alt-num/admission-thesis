<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamChoice extends Model
{
    use HasFactory;

    protected $primaryKey = 'choice_id';

    protected $guarded = [];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id', 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class, 'choice_id', 'choice_id');
    }
}
