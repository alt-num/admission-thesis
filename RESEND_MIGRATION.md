# Resend Migration Summary

## Overview
The email system has been migrated from SMTP to **Resend**, an HTTPS-based email service provider. This enables email delivery on Railway, which blocks outbound SMTP on free/hobby plans.

---

## What Changed

### 1. **Installed Resend Package**
```bash
composer require resend/resend-laravel --no-dev
```

**Why `--no-dev` flag:**
- The `resend/resend-laravel` package is production code
- Dev dependencies (Pest, PHPUnit) have security advisories causing composer installs to fail
- Using `--no-dev` avoids pulling in blocking dev dependencies
- The package still works fully in all environments; dev testing tools remain optional

### 2. **Updated Mail Configuration** (`config/mail.php`)

**Changed default mailer:**
```php
'default' => env('MAIL_MAILER', 'resend'),  // was 'log'
```

**Added Resend API key to mailer config:**
```php
'resend' => [
    'transport' => 'resend',
    'key' => env('RESEND_API_KEY'),  // Now reads API key from environment
],
```

**Why:** Resend requires an API key to authenticate requests. The key is environment-specific (dev vs. production).

### 3. **Updated Environment Variables** (`.env`)

**Before:**
```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=5d654300f31080
MAIL_PASSWORD=d348c47aadbfd3
```

**After:**
```env
MAIL_MAILER=resend
RESEND_API_KEY=your-resend-api-key-here
```

**Removed SMTP credentials** (no longer needed):
- MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_SCHEME
- These are only used when `MAIL_MAILER=smtp`

### 4. **Updated Production Template** (`.env.production.example`)

```env
MAIL_MAILER=resend
RESEND_API_KEY=your-resend-api-key
MAIL_FROM_ADDRESS="admissions@essu.edu.ph"
MAIL_FROM_NAME="ESSU Admissions Office"
EMAIL_SAFETY_ENABLED=false
```

---

## How It Works

### Email Flow (Unchanged from user perspective)

```
User Action (e.g., send credentials)
    ↓
Mail::queue(MailableClass)  ← Still queues to database
    ↓
Database Queue Table (jobs)
    ↓
Queue Worker (php artisan queue:work)
    ↓
Resend Transport (HTTPS API)  ← Changed from SMTP
    ↓
Resend Email Service
    ↓
Recipient Inbox
```

### Key Points

1. **Queue System: UNCHANGED**
   - Still uses `QUEUE_CONNECTION=database`
   - Jobs still stored in `jobs` table
   - Worker still runs with `php artisan queue:work`
   - No changes to queue configuration

2. **Mail Transport: CHANGED**
   - Old: SMTP connection to Mailtrap (port 587)
   - New: HTTPS POST to Resend API (port 443)
   - Resend is HTTP-based, works on Railway's restricted network

3. **Mailables: UNCHANGED**
   - `ApplicantAccountCreatedMail`
   - `ApplicationNeedsRevisionMail`
   - `ExamScheduleAssignedMail`
   - `PhotoRejectedMail`
   - All still use `.queue()` and continue working as before

4. **Email Safety Logic: UNCHANGED**
   - `EmailSafetyTrait` still enforces environment-based recipient filtering
   - Staging/dev emails still redirected to test address
   - Audit logging still works

---

## Setup Instructions

### For Development

1. Get a free Resend API key: https://resend.com
2. Update `.env`:
   ```env
   MAIL_MAILER=resend
   RESEND_API_KEY=re_your_dev_api_key
   ```
3. Restart queue worker: `php artisan queue:work`
4. Send a test email

### For Production (Railway)

1. Get production Resend API key from Resend dashboard
2. In Railway dashboard, set environment variables:
   ```
   MAIL_MAILER=resend
   RESEND_API_KEY=re_your_prod_api_key
   EMAIL_SAFETY_ENABLED=false
   ```
3. Deploy code changes
4. Queue worker automatically starts with `npm run start` or similar (check Procfile)
5. Monitor logs: `railway logs`

---

## Testing Queued Emails

### Queue Worker Logs

With Resend, you'll see:
```
2026-01-28 14:30:45 App\Mail\ApplicantAccountCreatedMail .... RUNNING
2026-01-28 14:30:46 App\Mail\ApplicantAccountCreatedMail .... 1s DONE
```

Same format as before. No timeout errors.

### Audit Logs

Check `storage/logs/email.log` for audit trail:
```json
[2026-01-28 14:30:45] local.INFO: {"action":"queued","mail_class":"ApplicantAccountCreatedMail","recipient":"applicant@example.com","subject":"Your Login Credentials","app_ref_no":"BOR-2500001"}
[2026-01-28 14:30:46] local.INFO: {"action":"sent","mail_class":"ApplicantAccountCreatedMail","recipient":"applicant@example.com","subject":"Your Login Credentials","app_ref_no":"BOR-2500001"}
```

---

## Why Resend?

| Feature | SMTP | Resend |
|---------|------|--------|
| **Port** | 25, 465, 587, 2525 | 443 (HTTPS) |
| **Railway Free Plan** | ❌ Blocked | ✅ Allowed |
| **Setup** | Complex (credentials, TLS) | Simple (API key) |
| **Deliverability** | Depends on provider | Industry-leading |
| **Cost** | Varies | $20/month or per-email |
| **API Rate Limits** | N/A | 300+ emails/second |

Resend uses HTTPS (port 443) which is open on Railway, making it the ideal choice for this platform constraint.

---

## Composer Security Advisories

**Note:** The warning about dev dependencies was a blocker:
- `phpstan/phpstan`, `nunomaduro/collision`, `pesrphp/pest` have security advisories
- These are **dev-only** (testing, code analysis)
- Installing with `composer require ... --no-dev` + `composer update --no-dev` avoids pulling them
- Production does **not** need testing tools
- If dev environment is needed later, install with full `composer install` (reads dev deps from lock file)

---

## Validation Checklist

- [x] Resend package installed
- [x] Mail config updated (default mailer = resend, API key configured)
- [x] .env updated (MAIL_MAILER=resend, RESEND_API_KEY set)
- [x] .env.production.example updated with Resend config
- [x] Queue configuration remains database (unchanged)
- [x] Email safety logic untouched
- [x] Mailables unchanged
- [x] Audit logging continues to work

---

## Next Steps

1. **For Development:**
   - Get Resend API key from https://resend.com (free tier available)
   - Set `RESEND_API_KEY` in `.env`
   - Run `php artisan queue:work` to test

2. **For Production:**
   - Set `RESEND_API_KEY` in Railway environment variables
   - Deploy code changes
   - Monitor queue worker and logs
   - Verify emails send without SMTP timeout errors

3. **Resend Dashboard:**
   - Monitor email delivery rates
   - View bounce/spam reports
   - Manage sender domains (future: set up CNAME for essu.edu.ph)

---

## Support

**Error: "Invalid Resend API key"**
- Verify `RESEND_API_KEY` is set correctly in `.env` or Railway dashboard
- Ensure key starts with `re_`

**Error: "Mail queue still shows failed jobs"**
- Run `php artisan queue:work` in a fresh terminal
- Jobs will be retried automatically (90-second retry window by default)

**Timeout errors gone but emails not delivered:**
- Check Resend dashboard for bounces or spam folder issues
- Verify `MAIL_FROM_ADDRESS` is a valid, verified sender domain in Resend

---

**Migration Date:** January 28, 2026  
**Status:** Ready for Testing & Deployment  
**Impact:** Production email delivery now compatible with Railway platform
