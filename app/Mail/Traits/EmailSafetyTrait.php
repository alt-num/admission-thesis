<?php

namespace App\Mail\Traits;

use App\Services\EmailAuditService;

/**
 * Email Safety Trait
 * 
 * Provides methods to check and enforce environment-based email safety.
 * Use this in mail classes to prevent sending emails to real users in non-production.
 */
trait EmailSafetyTrait
{
    /**
     * Check if the email recipient is safe to send to in current environment.
     * 
     * Returns the actual recipient to use (may be redirected to test address).
     * Returns null if email should not be sent.
     */
    protected function getSafeRecipient(string $originalRecipient, ?string $appRefNo = null): ?string
    {
        // If safety is disabled, always send to original recipient
        if (!config('email-safety.enabled')) {
            return $originalRecipient;
        }

        // If in production environment, always send to original recipient
        $currentEnv = app()->environment();
        $productionEnvs = config('email-safety.production_environments', ['production']);
        
        if (in_array($currentEnv, $productionEnvs)) {
            return $originalRecipient;
        }

        // Not in production - check if recipient is in safe domains
        $recipientDomain = substr(strrchr($originalRecipient, '@'), 1);
        $safeDomains = config('email-safety.safe_domains', ['example.local', 'localhost']);
        
        if (in_array($recipientDomain, $safeDomains)) {
            return $originalRecipient;
        }

        // Not safe - redirect or block based on fallback mode
        $fallbackMode = config('email-safety.fallback_mode', 'redirect');
        
        if ($fallbackMode === 'redirect') {
            $testRecipient = config('email-safety.test_recipient', 'admin@example.local');
            $subject = $this->envelope()->subject ?? 'Email';
            EmailAuditService::logRedirected(
                static::class,
                $originalRecipient,
                $testRecipient,
                $subject,
                $appRefNo
            );
            return $testRecipient;
        }

        // Log mode - block send
        $subject = $this->envelope()->subject ?? 'Email';
        EmailAuditService::logBlocked(
            static::class,
            $originalRecipient,
            $subject,
            'Non-production environment, fallback mode is log',
            $appRefNo
        );
        return null;
    }

    /**
     * Check if recipient is in a safe domain (can receive in any environment).
     */
    protected function isInSafeDomain(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);
        $safeDomains = config('email-safety.safe_domains', ['example.local', 'localhost']);
        return in_array($domain, $safeDomains);
    }

    /**
     * Check if current environment is production.
     */
    protected function isProduction(): bool
    {
        $productionEnvs = config('email-safety.production_environments', ['production']);
        return in_array(app()->environment(), $productionEnvs);
    }
}
