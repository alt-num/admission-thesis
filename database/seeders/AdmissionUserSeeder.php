<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdmissionUser;

class AdmissionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Admin account
        AdmissionUser::create([
            'employee_id' => 1,
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Staff account
        AdmissionUser::create([
            'employee_id' => 2,
            'username' => 'staff',
            'password' => Hash::make('password123'),
            'role' => 'Staff',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

