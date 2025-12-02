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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id('attempt_id');
            $table->foreignId('exam_id')
                ->constrained('exams', 'exam_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->uuid('session_id')->nullable();
            $table->foreignId('applicant_id')
                ->constrained('applicants', 'applicant_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->string('start_ip', 45)->nullable();
            $table->boolean('ip_changed')->default(false);
            $table->timestamp('finished_at')->nullable();
            $table->decimal('score_total', 5, 2)->default(0);
            $table->decimal('score_verbal', 5, 2)->default(0);
            $table->decimal('score_nonverbal', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['applicant_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};

