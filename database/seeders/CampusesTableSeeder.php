<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $campuses = [
            // Main Campus — Borongan
            [
                'campus_name' => 'Main Campus',
                'campus_code' => 'MAIN',
                'barangay' => 'Maydolong',
                'city_name' => 'Borongan',
                'city_code' => 'BOR',
                'province' => 'Eastern Samar',
                'full_address' => 'Brgy. Maydolong, Borongan City, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Guiuan Campus
            [
                'campus_name' => 'Guiuan Campus',
                'campus_code' => 'GUIUAN',
                'barangay' => 'Salug',
                'city_name' => 'Guiuan',
                'city_code' => 'GUI',
                'province' => 'Eastern Samar',
                'full_address' => 'Brgy. Salug, Guiuan City, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Can-Avid Campus
            [
                'campus_name' => 'Can-Avid Campus',
                'campus_code' => 'CAN-AVID',
                'barangay' => '10',
                'city_name' => 'Can-Avid',
                'city_code' => 'CAN',
                'province' => 'Eastern Samar',
                'full_address' => 'Brgy. 10, Can-Avid, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Arteche Campus
            [
                'campus_name' => 'Arteche Campus',
                'campus_code' => 'ARTECHE',
                'barangay' => 'Balud',
                'city_name' => 'Arteche',
                'city_code' => 'ART',
                'province' => 'Eastern Samar',
                'full_address' => 'Brgy. Balud, Arteche, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('campuses')->insert($campuses);
    }
}
