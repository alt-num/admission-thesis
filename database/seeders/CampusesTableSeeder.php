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
            [
                'campus_name' => 'Main Campus',
                'campus_code' => 'MAIN',
                'address' => 'Brgy. Maydolong, Borongan City, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'campus_name' => 'Guiuan Campus',
                'campus_code' => 'GUIUAN',
                'address' => 'Brgy. Salug, Guiuan, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'campus_name' => 'Can-Avid Campus',
                'campus_code' => 'CAN-AVID',
                'address' => 'Brgy. 10, Can-Avid, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'campus_name' => 'Arteche Campus',
                'campus_code' => 'ARTECHE',
                'address' => 'Brgy. Balud, Arteche, Eastern Samar',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('campuses')->insert($campuses);
    }
}
