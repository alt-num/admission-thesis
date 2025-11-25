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
        Schema::create('applicant_exam_schedules', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('applicant_id')
                ->constrained('applicants', 'applicant_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('schedule_id')
                ->constrained('exam_schedules', 'schedule_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamps();

            $table->unique(['applicant_id', 'schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_exam_schedules');
    }
};
