<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntiCheatSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'enabled',
        'tab_switch_detection',
        'focus_loss_violations',
        'copy_paste_blocking',
        'right_click_blocking',
        'devtools_hotkey_blocking',
        'ip_change_logging',
        'exam_code_required',
        'refresh_detection',
        'idle_timeout_minutes',
        'ip_check_strictness',
        'developer_bypass_enabled',
        'monitoring_banner_enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'tab_switch_detection' => 'boolean',
        'focus_loss_violations' => 'boolean',
        'copy_paste_blocking' => 'boolean',
        'right_click_blocking' => 'boolean',
        'devtools_hotkey_blocking' => 'boolean',
        'ip_change_logging' => 'boolean',
        'exam_code_required' => 'boolean',
        'refresh_detection' => 'boolean',
        'idle_timeout_minutes' => 'integer',
        'ip_check_strictness' => 'string',
        'developer_bypass_enabled' => 'boolean',
        'monitoring_banner_enabled' => 'boolean',
    ];

    /**
     * Get the current settings (singleton pattern).
     * Returns existing settings without overriding user values.
     */
    public static function current()
    {
        $settings = static::first();
        
        if (!$settings) {
            return static::create([
                'enabled' => true,
                'tab_switch_detection' => true,
                'focus_loss_violations' => true,
                'copy_paste_blocking' => true,
                'right_click_blocking' => true,
                'devtools_hotkey_blocking' => true,
                'ip_change_logging' => true,
                'exam_code_required' => true,
                'refresh_detection' => true,
                'idle_timeout_minutes' => 10,
                'ip_check_strictness' => 'log_only',
                'developer_bypass_enabled' => false,
                'monitoring_banner_enabled' => false,
            ]);
        }
        
        // Only migrate idle_timeout_minutes if it's null, 0, or old default (2)
        // Do NOT override existing user values
        $needsSave = false;
        if (is_null($settings->getRawOriginal('idle_timeout_minutes')) || 
            $settings->getRawOriginal('idle_timeout_minutes') == 0 || 
            $settings->getRawOriginal('idle_timeout_minutes') == 2) {
            $settings->idle_timeout_minutes = 10;
            $needsSave = true;
        }
        
        // Ensure new fields have defaults if they don't exist (for existing installations)
        if (is_null($settings->getRawOriginal('refresh_detection'))) {
            $settings->refresh_detection = true;
            $needsSave = true;
        }
        
        if (is_null($settings->getRawOriginal('monitoring_banner_enabled'))) {
            $settings->monitoring_banner_enabled = false;
            $needsSave = true;
        }
        
        if ($needsSave) {
            $settings->save();
        }
        
        return $settings;
    }
    
    /**
     * Get idle timeout minutes with default fallback.
     */
    public function getIdleTimeoutMinutesAttribute($value)
    {
        // If value is null or 0, return default
        if (is_null($value) || $value == 0) {
            return 10;
        }
        return $value;
    }

    /**
     * Check if anti-cheat should be enabled (respects developer bypass).
     */
    public function isEnabled()
    {
        if (!$this->enabled) {
            return false;
        }

        // Developer bypass: disable if enabled and in local environment
        if ($this->developer_bypass_enabled && app()->environment('local')) {
            return false;
        }

        return true;
    }
}
