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
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Drop the foreign key constraint first if it exists
            $table->dropForeign(['session_id']);
        });

        // For PostgreSQL: Change column type to UUID
        // First, clear existing foreign key values (they're not UUIDs)
        DB::statement('UPDATE exam_attempts SET session_id = NULL');
        
        // Change column type to UUID and make it nullable
        DB::statement('ALTER TABLE exam_attempts ALTER COLUMN session_id TYPE UUID USING NULL');
        DB::statement('ALTER TABLE exam_attempts ALTER COLUMN session_id DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear UUID values before converting back
        DB::statement('UPDATE exam_attempts SET session_id = NULL');

        // Change back to bigInteger
        DB::statement('ALTER TABLE exam_attempts ALTER COLUMN session_id TYPE BIGINT');
        
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Re-add foreign key constraint (commented out - may need exam_sessions table)
            // $table->foreign('session_id')->references('session_id')->on('exam_sessions');
        });
    }
};
