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
        DB::statement("ALTER TABLE applicants DROP CONSTRAINT IF EXISTS applicants_status_check");
        
        // Update old status values to new ones
        DB::statement("UPDATE applicants SET status = 'Qualified' WHERE status = 'Passed'");
        DB::statement("UPDATE applicants SET status = 'NotQualified' WHERE status = 'Failed'");
        DB::statement("UPDATE applicants SET status = 'Pending' WHERE status = 'ExamTaken'");
        
        // Add new enum constraint with new values
        DB::statement("ALTER TABLE applicants ADD CONSTRAINT applicants_status_check CHECK (status IN ('Pending', 'Qualified', 'NotQualified'))");
        DB::statement("ALTER TABLE applicants ALTER COLUMN status SET DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new enum constraint
        DB::statement("ALTER TABLE applicants DROP CONSTRAINT IF EXISTS applicants_status_check");
        
        // Revert new status values to old ones
        DB::statement("UPDATE applicants SET status = 'Passed' WHERE status = 'Qualified'");
        DB::statement("UPDATE applicants SET status = 'Failed' WHERE status = 'NotQualified'");
        // Note: ExamTaken conversion is not reversible accurately, so we leave Pending as-is
        
        // Add back old enum constraint
        DB::statement("ALTER TABLE applicants ADD CONSTRAINT applicants_status_check CHECK (status IN ('Pending', 'ExamTaken', 'Passed', 'Failed'))");
        DB::statement("ALTER TABLE applicants ALTER COLUMN status SET DEFAULT 'Pending'");
    }
};
