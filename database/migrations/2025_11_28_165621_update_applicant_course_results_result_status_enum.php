<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old enum constraint
        DB::statement("ALTER TABLE applicant_course_results DROP CONSTRAINT IF EXISTS applicant_course_results_result_status_check");
        
        // Update old status values to new ones
        DB::statement("UPDATE applicant_course_results SET result_status = 'Qualified' WHERE result_status = 'Pass'");
        DB::statement("UPDATE applicant_course_results SET result_status = 'NotQualified' WHERE result_status = 'Fail'");
        
        // Add new enum constraint with new values
        DB::statement("ALTER TABLE applicant_course_results ADD CONSTRAINT applicant_course_results_result_status_check CHECK (result_status IN ('Qualified', 'NotQualified'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new enum constraint
        DB::statement("ALTER TABLE applicant_course_results DROP CONSTRAINT IF EXISTS applicant_course_results_result_status_check");
        
        // Revert new status values to old ones
        DB::statement("UPDATE applicant_course_results SET result_status = 'Pass' WHERE result_status = 'Qualified'");
        DB::statement("UPDATE applicant_course_results SET result_status = 'Fail' WHERE result_status = 'NotQualified'");
        
        // Add back old enum constraint
        DB::statement("ALTER TABLE applicant_course_results ADD CONSTRAINT applicant_course_results_result_status_check CHECK (result_status IN ('Pass', 'Fail'))");
    }
};
