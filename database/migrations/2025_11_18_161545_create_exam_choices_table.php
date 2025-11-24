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
        Schema::create('exam_choices', function (Blueprint $table) {
            $table->id('choice_id');
            $table->foreignId('question_id')
                ->constrained('exam_questions', 'question_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('choice_text')->nullable();
            $table->string('choice_image')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_choices');
    }
};
