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
        Schema::create('anti_cheat_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('applicant_id')
                ->constrained('applicants', 'applicant_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')
                ->nullable()
                ->constrained('exam_attempts', 'attempt_id')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('event_type', 100);
            $table->json('event_details')->nullable();
            $table->timestamp('event_timestamp');
            $table->timestamps();

            $table->index(['applicant_id', 'exam_attempt_id']);
            $table->index('event_type');
            $table->index('event_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anti_cheat_logs');
    }
};

