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
        Schema::create('applicant_users', function (Blueprint $table) {
            $table->id('user_id');
            $table->foreignId('applicant_id')
                ->constrained('applicants', 'applicant_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('account_status')->default('active');
            $table->string('plain_password', 10)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_users');
    }
};

