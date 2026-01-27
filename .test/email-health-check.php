#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   ESSU ADMISSION - EMAIL SYSTEM HEALTH CHECK            ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$passed = 0;
$failed = 0;

// Check 1: SMTP Configuration
echo "[1] SMTP Configuration\n";
try {
    $mailer = config('mail.default');
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    $username = config('mail.mailers.smtp.username');
    
    if ($mailer === 'log') {
        echo "    ❌ FAILED - Mail mailer is set to 'log' (development mode)\n";
        echo "       Set MAIL_MAILER=smtp for production\n";
        $failed++;
    } elseif (!$host || !$port) {
        echo "    ❌ FAILED - SMTP host/port not configured\n";
        echo "       Configure MAIL_HOST and MAIL_PORT in .env\n";
        $failed++;
    } else {
        echo "    ✅ PASSED - SMTP configured\n";
        echo "       Mailer: {$mailer}, Host: {$host}:{$port}\n";
        $passed++;
    }
} catch (\Exception $e) {
    echo "    ❌ ERROR - " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n";

// Check 2: From Address Configuration
echo "[2] From Address Configuration\n";
try {
    $fromAddress = config('mail.from.address');
    $fromName = config('mail.from.name');
    
    if (strpos($fromAddress, 'example.com') !== false) {
        echo "    ❌ FAILED - From address is placeholder 'example.com'\n";
        echo "       Update MAIL_FROM_ADDRESS to your actual domain\n";
        $failed++;
    } else {
        echo "    ✅ PASSED - From address configured\n";
        echo "       Address: {$fromAddress}, Name: {$fromName}\n";
        $passed++;
    }
} catch (\Exception $e) {
    echo "    ❌ ERROR - " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n";

// Check 3: Queue Configuration
echo "[3] Queue Configuration\n";
try {
    $driver = config('queue.default');
    
    if ($driver !== 'database') {
        echo "    ⚠️  WARNING - Queue driver is '{$driver}' (expected 'database')\n";
        echo "       System will still work, but database queue is recommended\n";
        $passed++;
    } else {
        echo "    ✅ PASSED - Queue driver is 'database'\n";
        $passed++;
    }
} catch (\Exception $e) {
    echo "    ❌ ERROR - " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n";

// Check 4: Queue Worker Status
echo "[4] Queue Worker Status\n";
echo "    ℹ️  To verify queue worker is running, execute:\n";
echo "       ps aux | grep 'queue:work'\n";
echo "    For Supervisor:\n";
echo "       sudo supervisorctl status admission-queue-worker:*\n";

echo "\n";

// Check 5: Email Audit Logging
echo "[5] Email Audit Logging\n";
try {
    $emailLogChannel = 'email';
    $config = config("logging.channels.{$emailLogChannel}");
    
    if ($config) {
        echo "    ✅ PASSED - Email audit logging configured\n";
        echo "       Driver: " . $config['driver'] . "\n";
        echo "       Path: " . $config['path'] . "\n";
        $passed++;
    } else {
        echo "    ❌ FAILED - Email audit logging not configured\n";
        $failed++;
    }
} catch (\Exception $e) {
    echo "    ❌ ERROR - " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n";

// Check 6: Email Safety Configuration
echo "[6] Email Safety Configuration\n";
try {
    $safetyEnabled = config('email-safety.enabled');
    $productionEnvs = config('email-safety.production_environments');
    $currentEnv = app()->environment();
    
    if (!config('email-safety')) {
        echo "    ⚠️  WARNING - Email safety config file not found\n";
        echo "       Run: php artisan vendor:publish --provider=EmailSafetyProvider\n";
    } else {
        $isProd = in_array($currentEnv, $productionEnvs);
        echo "    ℹ️  Current Environment: {$currentEnv}\n";
        echo "    ℹ️  Safety Enabled: " . ($safetyEnabled ? 'YES' : 'NO') . "\n";
        echo "    ℹ️  Production Environments: " . implode(', ', $productionEnvs) . "\n";
        
        if ($isProd && $safetyEnabled) {
            echo "    ❌ WARNING - Safety enabled in production!\n";
            echo "       Disable safety in production by setting:\n";
            echo "       EMAIL_SAFETY_ENABLED=false\n";
        } else {
            echo "    ✅ PASSED - Email safety configured appropriately\n";
            $passed++;
        }
    }
} catch (\Exception $e) {
    echo "    ℹ️  Email safety config not fully configured: " . $e->getMessage() . "\n";
}

echo "\n";

// Check 7: Database Connection
echo "[7] Database Connection\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "    ✅ PASSED - Database connection successful\n";
    $passed++;
} catch (\Exception $e) {
    echo "    ❌ FAILED - Cannot connect to database\n";
    echo "       Error: " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n";

// Check 8: Test Email Send (Optional)
echo "[8] Test Email Send (Optional)\n";
echo "    To test email sending, run:\n";
echo "    php artisan tinker\n";
echo "    >>> Mail::raw('Test', function(\$m) { \$m->to('test@example.com'); })->send();\n";

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   SUMMARY                                              ║\n";
echo "╠════════════════════════════════════════════════════════╣\n";
echo "║   Passed:  " . str_pad($passed, 52, ' ', STR_PAD_LEFT) . "║\n";
echo "║   Failed:  " . str_pad($failed, 52, ' ', STR_PAD_LEFT) . "║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

if ($failed > 0) {
    echo "❌ Email system is NOT ready for production\n";
    echo "   Fix the failed checks above before deploying\n";
    exit(1);
} else {
    echo "✅ Email system configuration looks good!\n";
    echo "   Remember to:\n";
    echo "   1. Start the queue worker process\n";
    echo "   2. Configure SPF/DKIM/DMARC DNS records\n";
    echo "   3. Monitor email and queue logs regularly\n";
    exit(0);
}
