<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anti_cheat_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->boolean('enabled')->default(true);
            $table->boolean('tab_switch_detection')->default(true);
            $table->boolean('focus_loss_violations')->default(true);
            $table->boolean('copy_paste_blocking')->default(true);
            $table->boolean('right_click_blocking')->default(true);
            $table->boolean('devtools_hotkey_blocking')->default(true);
            $table->boolean('ip_change_logging')->default(true);
            $table->boolean('exam_code_required')->default(true);
            $table->boolean('refresh_detection')->default(true);
            $table->integer('max_focus_violations')->default(5);
            $table->integer('idle_timeout_minutes')->default(10);
            $table->string('ip_check_strictness', 20)->default('log_only');
            $table->boolean('developer_bypass_enabled')->default(false);
            $table->boolean('monitoring_banner_enabled')->default(false);
            $table->timestamps();
        });

        // Insert default settings (only if table is empty)
        if (DB::table('anti_cheat_settings')->count() === 0) {
            DB::table('anti_cheat_settings')->insert([
                'enabled' => true,
                'tab_switch_detection' => true,
                'focus_loss_violations' => true,
                'copy_paste_blocking' => true,
                'right_click_blocking' => true,
                'devtools_hotkey_blocking' => true,
                'ip_change_logging' => true,
                'exam_code_required' => true,
                'refresh_detection' => true,
                'max_focus_violations' => 5,
                'idle_timeout_minutes' => 10,
                'ip_check_strictness' => 'log_only',
                'developer_bypass_enabled' => false,
                'monitoring_banner_enabled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anti_cheat_settings');
    }
};

