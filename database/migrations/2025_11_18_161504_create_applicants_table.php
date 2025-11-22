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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id('applicant_id');
            $table->string('app_ref_no')->unique();
            $table->string('school_year');
            $table->foreignId('campus_id')
                ->constrained('campuses', 'campus_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('place_of_birth');
            $table->enum('sex', ['Male', 'Female']);
            $table->string('civil_status');
            $table->string('email');
            $table->string('contact_number', 32)->nullable();
            $table->string('barangay')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('last_school_attended')->nullable();
            $table->string('school_address')->nullable();
            $table->string('year_graduated', 9)->nullable();
            $table->decimal('gen_average', 5, 2)->nullable();
            $table->foreignId('preferred_course_1')
                ->constrained('courses', 'course_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('preferred_course_2')
                ->nullable()
                ->constrained('courses', 'course_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('preferred_course_3')
                ->nullable()
                ->constrained('courses', 'course_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->enum('status', ['Pending', 'ExamTaken', 'Passed', 'Failed'])->default('Pending');
            $table->foreignId('registered_by')
                ->nullable()
                ->constrained('admission_users', 'admission_user_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
