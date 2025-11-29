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
        Schema::table('anti_cheat_settings', function (Blueprint $table) {
            $table->boolean('refresh_detection')->default(true)->after('exam_code_required');
            $table->boolean('monitoring_banner_enabled')->default(false)->after('developer_bypass_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anti_cheat_settings', function (Blueprint $table) {
            $table->dropColumn(['refresh_detection', 'monitoring_banner_enabled']);
        });
    }
};
