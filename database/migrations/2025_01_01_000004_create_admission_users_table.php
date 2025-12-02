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
        Schema::create('admission_users', function (Blueprint $table) {
            $table->id('admission_user_id');
            $table->foreignId('employee_id')
                ->constrained('employees', 'employee_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['Admin', 'Staff'])->default('Staff');
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
        Schema::dropIfExists('admission_users');
    }
};

