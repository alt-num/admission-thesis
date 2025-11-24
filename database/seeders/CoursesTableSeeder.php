<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Fetch all department IDs using correct faculty codes
        $cagId = DB::table('departments')->where('faculty_code', 'CAG')->value('department_id');
        $casId = DB::table('departments')->where('faculty_code', 'CAS')->value('department_id');
        $cbmaId = DB::table('departments')->where('faculty_code', 'CBMA')->value('department_id');
        $ccsId = DB::table('departments')->where('faculty_code', 'CCS')->value('department_id');
        $ccjeId = DB::table('departments')->where('faculty_code', 'CCJE')->value('department_id');
        $coedId = DB::table('departments')->where('faculty_code', 'COED')->value('department_id');
        $coeId = DB::table('departments')->where('faculty_code', 'COE')->value('department_id');
        $cfasId = DB::table('departments')->where('faculty_code', 'CFAS')->value('department_id');
        $chmId = DB::table('departments')->where('faculty_code', 'CHM')->value('department_id');
        $cnasId = DB::table('departments')->where('faculty_code', 'CNAS')->value('department_id');
        $csId = DB::table('departments')->where('faculty_code', 'CS')->value('department_id');
        $cotId = DB::table('departments')->where('faculty_code', 'COT')->value('department_id');

        $courses = [
            // College of Agriculture
            [
                'course_code' => 'BSA',
                'course_name' => 'BS in Agriculture',
                'department_id' => $cagId,
                'passing_score' => 25,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Arts and Sciences
            [
                'course_code' => 'BACOM',
                'course_name' => 'BA in Communication',
                'department_id' => $casId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BAPS',
                'course_name' => 'BA in Political Science',
                'department_id' => $casId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSSW',
                'course_name' => 'BS in Social Work',
                'department_id' => $casId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Business Management and Accountancy
            [
                'course_code' => 'BSAc',
                'course_name' => 'BS in Accountancy',
                'department_id' => $cbmaId,
                'passing_score' => 35,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSAIS',
                'course_name' => 'BS in Accounting Information System',
                'department_id' => $cbmaId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSE',
                'course_name' => 'BS in Entrepreneurship',
                'department_id' => $cbmaId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSBA',
                'course_name' => 'BS in Business Administration',
                'department_id' => $cbmaId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSBA-BE',
                'course_name' => 'BSBA Major in Business Economics',
                'department_id' => $cbmaId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSBA-FM',
                'course_name' => 'BSBA Major in Financial Management',
                'department_id' => $cbmaId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSBA-HRM',
                'course_name' => 'BSBA Major in Human Resource Management',
                'department_id' => $cbmaId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSBA-MM',
                'course_name' => 'BSBA Major in Marketing Management',
                'department_id' => $cbmaId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Computer Studies
            [
                'course_code' => 'ACT',
                'course_name' => 'Associate in Computer Technology',
                'department_id' => $ccsId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSCS',
                'course_name' => 'BS in Computer Science',
                'department_id' => $ccsId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSEMC',
                'course_name' => 'BS in Entertainment and Multimedia Computing',
                'department_id' => $ccsId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSIT',
                'course_name' => 'BS in Information Technology',
                'department_id' => $ccsId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Criminal Justice Education
            [
                'course_code' => 'BSCrim',
                'course_name' => 'BS in Criminology',
                'department_id' => $ccjeId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Education
            [
                'course_code' => 'BEED',
                'course_name' => 'Bachelor of Elementary Education',
                'department_id' => $coedId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSED',
                'course_name' => 'Bachelor of Secondary Education',
                'department_id' => $coedId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Engineering
            [
                'course_code' => 'BSCE',
                'course_name' => 'BS in Civil Engineering',
                'department_id' => $coeId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSCpE',
                'course_name' => 'BS in Computer Engineering',
                'department_id' => $coeId,
                'passing_score' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSEE',
                'course_name' => 'BS in Electrical Engineering',
                'department_id' => $coeId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Fisheries and Aquatic Sciences
            [
                'course_code' => 'BSF',
                'course_name' => 'BS in Fisheries',
                'department_id' => $cfasId,
                'passing_score' => 25,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Hospitality Management
            [
                'course_code' => 'BSHM',
                'course_name' => 'BS in Hospitality Management',
                'department_id' => $chmId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSTM',
                'course_name' => 'BS in Tourism Management',
                'department_id' => $chmId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Nursing and Allied Sciences
            [
                'course_code' => 'BSMid',
                'course_name' => 'BS in Midwifery',
                'department_id' => $cnasId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSN',
                'course_name' => 'BS in Nursing',
                'department_id' => $cnasId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSND',
                'course_name' => 'BS in Nutrition and Dietetics',
                'department_id' => $cnasId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Science
            [
                'course_code' => 'BSBio',
                'course_name' => 'BS in Biology',
                'department_id' => $csId,
                'passing_score' => 28,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'course_code' => 'BSES',
                'course_name' => 'BS in Environmental Science',
                'department_id' => $csId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        
            // College of Technology
            [
                'course_code' => 'BIT',
                'course_name' => 'Bachelor in Industrial Technology',
                'department_id' => $cotId,
                'passing_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];        
        

        DB::table('courses')->insert(array_filter(
            $courses,
            fn (array $course) => ! is_null($course['department_id'])
        ));
    }
}
