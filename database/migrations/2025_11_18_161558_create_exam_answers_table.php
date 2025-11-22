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
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id('answer_id');
            $table->foreignId('attempt_id')
                ->constrained('exam_attempts', 'attempt_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('question_id')
                ->constrained('exam_questions', 'question_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('choice_id')
                ->nullable()
                ->constrained('exam_choices', 'choice_id')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->boolean('answer_value')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
