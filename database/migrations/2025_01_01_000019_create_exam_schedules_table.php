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
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->foreignId('exam_id')
                ->constrained('exams', 'exam_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->nullable();
            $table->string('location')->nullable();
            $table->string('exam_code', 5)->nullable();
            $table->boolean('anti_cheat_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};

