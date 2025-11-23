<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CampusesTableSeeder::class,
            DepartmentsTableSeeder::class,
            CoursesTableSeeder::class,
            Employees::class,
            AdmissionUserSeeder::class,
            #ApplicantUserSeeder::class,
        ]);
    }
}

