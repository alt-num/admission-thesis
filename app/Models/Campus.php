<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    use HasFactory;

    protected $primaryKey = 'campus_id';

    protected $fillable = [
        'campus_name',
        'campus_code',
        'barangay',
        'city_name',
        'city_code',
        'province',
        'full_address',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'campus_id', 'campus_id');
    }
}
