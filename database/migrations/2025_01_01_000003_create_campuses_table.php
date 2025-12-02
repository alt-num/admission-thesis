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
        Schema::create('campuses', function (Blueprint $table) {
            $table->id('campus_id');
            $table->string('campus_name');
            $table->string('campus_code')->unique();
            $table->string('barangay')->nullable();
            $table->string('city_name');
            $table->string('city_code', 3);
            $table->string('province');
            $table->string('full_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};

