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
        Schema::create('applicant_course_results', function (Blueprint $table) {
            $table->id('result_id');
            $table->foreignId('applicant_id')
                ->constrained('applicants', 'applicant_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('course_id')
                ->constrained('courses', 'course_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('result_status', ['Qualified', 'NotQualified', 'Missed']);
            $table->decimal('score_value', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_course_results');
    }
};

