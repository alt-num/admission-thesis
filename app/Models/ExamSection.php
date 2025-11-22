<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSection extends Model
{
    use HasFactory;

    protected $primaryKey = 'section_id';

    protected $guarded = [];

    public function subsections(): HasMany
    {
        return $this->hasMany(ExamSubsection::class, 'section_id', 'section_id');
    }
}
