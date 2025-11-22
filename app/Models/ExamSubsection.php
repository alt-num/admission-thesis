<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSubsection extends Model
{
    use HasFactory;

    protected $primaryKey = 'subsection_id';

    protected $guarded = [];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class, 'section_id', 'section_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class, 'subsection_id', 'subsection_id');
    }

    public function subsectionScores(): HasMany
    {
        return $this->hasMany(ExamSubsectionScore::class, 'subsection_id', 'subsection_id');
    }
}
