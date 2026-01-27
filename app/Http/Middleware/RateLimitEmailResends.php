<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Email Rate Limiting Middleware
 * 
 * Prevents abuse of email resend endpoints by limiting the number of
 * email sends per applicant per time period.
 */
class RateLimitEmailResends
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the applicant ID from the route parameter
        $applicantId = $request->route('applicant')?->applicant_id;
        
        if (!$applicantId) {
            return $next($request);
        }

        // Create a rate limit key based on applicant ID and endpoint
        $endpoint = $request->route()->getName();
        $key = "email-resend-{$applicantId}-{$endpoint}";
        
        // Allow maximum 3 emails per applicant per 60 minutes
        $maxAttempts = 3;
        $decayMinutes = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            // Too many attempts
            $retryAfter = RateLimiter::availableIn($key);
            
            return back()->with('error', "Too many email sends. Please try again in {$retryAfter} seconds.");
        }

        // Record this attempt
        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
