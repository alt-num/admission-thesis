<?php

namespace App\Services;

use App\Models\AntiCheatSetting;

class AntiCheatSettingsService
{
    /**
     * Get the current anti-cheat settings from database.
     * Falls back to config defaults if database is not available.
     */
    public static function getSettings()
    {
        try {
            return AntiCheatSetting::current();
        } catch (\Exception $e) {
            // Database might not be available, return null to use config defaults
            return null;
        }
    }

    /**
     * Check if anti-cheat is enabled (respects developer bypass).
     */
    public static function isEnabled()
    {
        $settings = self::getSettings();
        
        if (!$settings) {
            return config('anticheat.enabled', true);
        }

        return $settings->isEnabled();
    }

    /**
     * Get a feature toggle value.
     */
    public static function getFeature($featureName, $default = true)
    {
        $settings = self::getSettings();
        
        if (!$settings) {
            return config("anticheat.features.{$featureName}", $default);
        }

        return $settings->{$featureName} ?? $default;
    }

    /**
     * Get max focus violations.
     */
    public static function getMaxFocusViolations()
    {
        $settings = self::getSettings();
        
        if (!$settings) {
            return config('anticheat.max_focus_violations', 5);
        }

        return $settings->max_focus_violations ?? 5;
    }

    /**
     * Get idle timeout minutes.
     */
    public static function getIdleTimeoutMinutes()
    {
        $settings = self::getSettings();
        
        if (!$settings) {
            return config('anticheat.idle_timeout_minutes', 10);
        }

        return $settings->idle_timeout_minutes ?? 10;
    }

    /**
     * Get IP check strictness.
     */
    public static function getIpCheckStrictness()
    {
        $settings = self::getSettings();
        
        if (!$settings) {
            return config('anticheat.ip_check_strictness', 'log_only');
        }

        return $settings->ip_check_strictness ?? 'log_only';
    }

    /**
     * Get all settings as an array for frontend use.
     */
    public static function getSettingsArray()
    {
        $settings = self::getSettings();
        
        if (!$settings) {
            return [
                'enabled' => config('anticheat.enabled', true),
                'tab_switch_detection' => config('anticheat.features.tab_switch_detection', true),
                'focus_loss_violations' => config('anticheat.features.focus_loss_violations', true),
                'copy_paste_blocking' => config('anticheat.features.copy_paste_blocking', true),
                'right_click_blocking' => config('anticheat.features.right_click_blocking', true),
                'devtools_hotkey_blocking' => config('anticheat.features.devtools_hotkey_blocking', true),
                'ip_change_logging' => config('anticheat.features.ip_change_logging', true),
                'exam_code_required' => config('anticheat.features.exam_code_required', true),
                'max_focus_violations' => config('anticheat.max_focus_violations', 5),
                'idle_timeout_minutes' => config('anticheat.idle_timeout_minutes', 10),
                'ip_check_strictness' => config('anticheat.ip_check_strictness', 'log_only'),
            ];
        }

        return [
            'enabled' => $settings->isEnabled(),
            'tab_switch_detection' => $settings->tab_switch_detection,
            'focus_loss_violations' => $settings->focus_loss_violations,
            'copy_paste_blocking' => $settings->copy_paste_blocking,
            'right_click_blocking' => $settings->right_click_blocking,
            'devtools_hotkey_blocking' => $settings->devtools_hotkey_blocking,
            'ip_change_logging' => $settings->ip_change_logging,
            'exam_code_required' => $settings->exam_code_required,
            // Accessors will ensure Balanced Mode defaults (5 and 10) are returned
            'max_focus_violations' => $settings->max_focus_violations ?? 5,
            'idle_timeout_minutes' => $settings->idle_timeout_minutes ?? 10,
            'ip_check_strictness' => $settings->ip_check_strictness,
        ];
    }
}

