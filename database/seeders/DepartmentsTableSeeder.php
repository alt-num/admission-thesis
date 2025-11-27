<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $departments = [
            [
                'faculty_code' => 'ADM',
                'department_name' => 'Admission Office',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CAG',
                'department_name' => 'College of Agriculture',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CAS',
                'department_name' => 'College of Arts and Sciences',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CBMA',
                'department_name' => 'College of Business Management and Accountancy',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CCS',
                'department_name' => 'College of Computer Studies',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CCJE',
                'department_name' => 'College of Criminal Justice Education',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'COED',
                'department_name' => 'College of Education',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'COE',
                'department_name' => 'College of Engineering',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CFAS',
                'department_name' => 'College of Fisheries and Aquatic Sciences',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CHM',
                'department_name' => 'College of Hospitality Management',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CNAS',
                'department_name' => 'College of Nursing and Allied Sciences',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'CS',
                'department_name' => 'College of Science',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'faculty_code' => 'COT',
                'department_name' => 'College of Technology',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        
        DB::table('departments')->insert($departments);
    }
}
