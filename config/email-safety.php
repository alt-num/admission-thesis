<?php

/**
 * Email Safety Configuration
 * 
 * This configuration prevents emails from being sent to real users
 * in non-production environments (staging, development).
 * 
 * In production, all emails send normally to configured recipients.
 * In other environments, emails are either redirected to test addresses
 * or logged instead of sent.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Email Safety Filtering
    |--------------------------------------------------------------------------
    |
    | When enabled, emails in non-production environments will be intercepted
    | and either sent to a test address or logged instead of sent to real users.
    |
    */
    'enabled' => env('EMAIL_SAFETY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Production Environment Names
    |--------------------------------------------------------------------------
    |
    | List of environment names that are considered "production".
    | Emails in these environments will NOT be filtered.
    | All other environments are treated as non-production.
    |
    */
    'production_environments' => explode(',', env('EMAIL_SAFETY_PRODUCTION_ENVS', 'production')),

    /*
    |--------------------------------------------------------------------------
    | Test Email Address
    |--------------------------------------------------------------------------
    |
    | In non-production environments, emails are redirected to this address
    | instead of being sent to real users. This allows testing without
    | spamming actual applicants.
    |
    */
    'test_recipient' => env('EMAIL_SAFETY_TEST_RECIPIENT', 'admin@example.local'),

    /*
    |--------------------------------------------------------------------------
    | Safe Recipient Domains
    |--------------------------------------------------------------------------
    |
    | Email addresses with these domains can receive emails in any environment.
    | Useful for internal staff addresses or approved test domains.
    | Format: comma-separated list of domains
    |
    | Example: 'example.local,test.example.com'
    |
    */
    'safe_domains' => explode(',', env('EMAIL_SAFETY_SAFE_DOMAINS', 'example.local,localhost')),

    /*
    |--------------------------------------------------------------------------
    | Fallback Mode
    |--------------------------------------------------------------------------
    |
    | When 'redirect': Redirect non-safe emails to test_recipient
    | When 'log': Log emails instead of sending (useful for testing without email service)
    |
    */
    'fallback_mode' => env('EMAIL_SAFETY_FALLBACK_MODE', 'redirect'),

    /*
    |--------------------------------------------------------------------------
    | Log Channel for Redirected Emails
    |--------------------------------------------------------------------------
    |
    | Which log channel to use for logging email details.
    | Only used when fallback_mode is 'log'.
    |
    */
    'log_channel' => env('EMAIL_SAFETY_LOG_CHANNEL', 'stack'),
];
