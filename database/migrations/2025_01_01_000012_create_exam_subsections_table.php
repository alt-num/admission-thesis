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
        Schema::create('exam_subsections', function (Blueprint $table) {
            $table->id('subsection_id');
            $table->foreignId('section_id')
                ->constrained('exam_sections', 'section_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('order_no')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_subsections');
    }
};

