<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;

/**
 * Email Audit Service
 * 
 * Logs all email send attempts (success, queue, failure) for audit and monitoring.
 * This helps track email delivery issues and provides a complete audit trail.
 */
class EmailAuditService
{
    /**
     * Log an email as queued for sending.
     */
    public static function logQueued(string $mailableClass, string $recipient, string $subject, ?string $appRefNo = null): void
    {
        Log::channel('email')->info('Email queued', [
            'mailable' => $mailableClass,
            'recipient' => $recipient,
            'subject' => $subject,
            'app_ref_no' => $appRefNo,
            'status' => 'queued',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log an email as sent successfully.
     */
    public static function logSent(string $mailableClass, string $recipient, string $subject, ?string $appRefNo = null): void
    {
        Log::channel('email')->info('Email sent', [
            'mailable' => $mailableClass,
            'recipient' => $recipient,
            'subject' => $subject,
            'app_ref_no' => $appRefNo,
            'status' => 'sent',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log an email send failure.
     */
    public static function logFailed(string $mailableClass, string $recipient, string $subject, string $errorMessage, ?string $appRefNo = null): void
    {
        Log::channel('email')->error('Email send failed', [
            'mailable' => $mailableClass,
            'recipient' => $recipient,
            'subject' => $subject,
            'app_ref_no' => $appRefNo,
            'status' => 'failed',
            'error' => $errorMessage,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log an email that was redirected due to environment safety.
     */
    public static function logRedirected(string $mailableClass, string $originalRecipient, string $redirectedRecipient, string $subject, ?string $appRefNo = null): void
    {
        Log::channel('email')->warning('Email redirected (environment safety)', [
            'mailable' => $mailableClass,
            'original_recipient' => $originalRecipient,
            'redirected_recipient' => $redirectedRecipient,
            'subject' => $subject,
            'app_ref_no' => $appRefNo,
            'status' => 'redirected',
            'environment' => app()->environment(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log an email that was not sent due to environment safety.
     */
    public static function logBlocked(string $mailableClass, string $recipient, string $subject, string $reason, ?string $appRefNo = null): void
    {
        Log::channel('email')->warning('Email blocked (environment safety)', [
            'mailable' => $mailableClass,
            'recipient' => $recipient,
            'subject' => $subject,
            'app_ref_no' => $appRefNo,
            'status' => 'blocked',
            'reason' => $reason,
            'environment' => app()->environment(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
