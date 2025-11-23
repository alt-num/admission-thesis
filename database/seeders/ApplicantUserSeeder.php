<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\ApplicantUser;

class ApplicantUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Applicant user
        ApplicantUser::create([
            'applicant_id' => 1,
            'username' => 'applicant',
            'password' => Hash::make('password123'),
            'account_status' => 'Active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

