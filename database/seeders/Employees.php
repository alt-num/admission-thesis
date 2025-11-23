<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class Employees extends Seeder
{
    public function run(): void
    {
        $now = now();

        $employees = [
            [
                'first_name' => 'John',
                'middle_name' => 'Doe',
                'last_name' => 'Smith',
                'status' => 'active',
                'department_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'first_name' => 'Jane',
                'middle_name' => 'Marie',
                'last_name' => 'Johnson',
                'status' => 'active',
                'department_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('employees')->insert($employees);
    }
}