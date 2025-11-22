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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('subsection_id')
                ->constrained('exam_subsections', 'subsection_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('type', ['MCQ', 'TRUE_FALSE'])->default('MCQ');
            $table->unsignedInteger('order_no')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
