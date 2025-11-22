<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantDeclaration extends Model
{
    use HasFactory;

    protected $primaryKey = 'declaration_id';

    protected $guarded = [];

    protected $casts = [
        'physical_condition_flag' => 'boolean',
        'disciplinary_action_flag' => 'boolean',
        'certified_date' => 'date',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }
}
