<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admission_users';

    protected $primaryKey = 'admission_user_id';

    protected $fillable = [
        'employee_id',
        'username',
        'password',
        'plain_password',
        'role',
        'account_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function registeredApplicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'registered_by', 'admission_user_id');
    }
}
