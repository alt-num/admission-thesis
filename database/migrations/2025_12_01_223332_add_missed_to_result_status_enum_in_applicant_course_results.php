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
        
        // Add new enum constraint with 'Missed' value
        DB::statement("ALTER TABLE applicant_course_results ADD CONSTRAINT applicant_course_results_result_status_check CHECK (result_status IN ('Qualified', 'NotQualified', 'Missed'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new enum constraint
        DB::statement("ALTER TABLE applicant_course_results DROP CONSTRAINT IF EXISTS applicant_course_results_result_status_check");
        
        // Update any 'Missed' values back to 'NotQualified' before removing the enum value
        DB::statement("UPDATE applicant_course_results SET result_status = 'NotQualified' WHERE result_status = 'Missed'");
        
        // Add back old enum constraint
        DB::statement("ALTER TABLE applicant_course_results ADD CONSTRAINT applicant_course_results_result_status_check CHECK (result_status IN ('Qualified', 'NotQualified'))");
    }
};
