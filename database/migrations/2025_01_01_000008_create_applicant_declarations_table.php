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
        Schema::create('applicant_declarations', function (Blueprint $table) {
            $table->id('declaration_id');
            $table->foreignId('applicant_id')
                ->constrained('applicants', 'applicant_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->boolean('physical_condition_flag')->default(false);
            $table->text('physical_condition_desc')->nullable();
            $table->boolean('disciplinary_action_flag')->default(false);
            $table->text('disciplinary_action_desc')->nullable();
            $table->string('certified_signature_name')->nullable();
            $table->date('certified_date')->nullable();
            $table->string('filled_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_declarations');
    }
};

