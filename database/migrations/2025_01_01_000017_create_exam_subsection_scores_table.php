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
        Schema::create('exam_subsection_scores', function (Blueprint $table) {
            $table->id('subsection_score_id');
            $table->foreignId('attempt_id')
                ->constrained('exam_attempts', 'attempt_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('subsection_id')
                ->constrained('exam_subsections', 'subsection_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_subsection_scores');
    }
};

