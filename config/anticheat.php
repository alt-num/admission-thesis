<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Anti-Cheat System Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration provides default values for the anti-cheat system.
    | Actual settings are stored in the database (anti_cheat_settings table)
    | and loaded dynamically through controllers/services.
    |
    | DO NOT add model or database calls here - config files load before DB.
    |
    */

    'enabled' => env('ANTICHEAT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles (Defaults)
    |--------------------------------------------------------------------------
    |
    | Default values for individual feature controls.
    | These are overridden by database settings when available.
    |
    */

    'features' => [
        'tab_switch_detection' => true,
        'focus_loss_violations' => true,
        'copy_paste_blocking' => true,
        'right_click_blocking' => true,
        'devtools_hotkey_blocking' => true,
        'ip_change_logging' => true,
        'exam_code_required' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Strictness Controls (Defaults)
    |--------------------------------------------------------------------------
    |
    | Default values for anti-cheat strictness levels.
    | These are overridden by database settings when available.
    |
    */

    'max_focus_violations' => 5,
    'idle_timeout_minutes' => 10,
    'ip_check_strictness' => env('ANTICHEAT_IP_CHECK_STRICTNESS', 'log_only'),

    /*
    |--------------------------------------------------------------------------
    | Developer Bypass (Default)
    |--------------------------------------------------------------------------
    |
    | Default value for developer bypass mode.
    | This is overridden by database settings when available.
    |
    */

    'developer_bypass_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Active Examinees Monitor
    |--------------------------------------------------------------------------
    |
    | Configuration for the active examinees monitor dashboard.
    |
    */

    'monitor' => [
        'idle_minutes' => env('ANTICHEAT_MONITOR_IDLE_MINUTES', 2),
        'refresh_interval' => env('ANTICHEAT_MONITOR_REFRESH_INTERVAL', 15), // seconds
    ],
];

