<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('anti_cheat_logs', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['exam_attempt_id']);
            
            // Drop the existing index that includes exam_attempt_id
            $table->dropIndex(['applicant_id', 'exam_attempt_id']);
        });
        
        // Use raw SQL to alter the column to nullable (more reliable across databases)
        \DB::statement('ALTER TABLE anti_cheat_logs ALTER COLUMN exam_attempt_id DROP NOT NULL');
        
        Schema::table('anti_cheat_logs', function (Blueprint $table) {
            // Re-add the foreign key constraint with nullable support
            $table->foreign('exam_attempt_id')
                ->references('attempt_id')
                ->on('exam_attempts')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            
            // Re-add the index (nullable columns are allowed in indexes)
            $table->index(['applicant_id', 'exam_attempt_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anti_cheat_logs', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['exam_attempt_id']);
            $table->dropIndex(['applicant_id', 'exam_attempt_id']);
        });
        
        // Use raw SQL to make the column NOT NULL again
        // Note: This will fail if there are any NULL values in the column
        \DB::statement('ALTER TABLE anti_cheat_logs ALTER COLUMN exam_attempt_id SET NOT NULL');
        
        Schema::table('anti_cheat_logs', function (Blueprint $table) {
            // Re-add the original foreign key constraint
            $table->foreign('exam_attempt_id')
                ->references('attempt_id')
                ->on('exam_attempts')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            // Re-add the index
            $table->index(['applicant_id', 'exam_attempt_id']);
        });
    }
};
