<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

$dbHost = Setting::get('smtp_host');
$dbPort = Setting::get('smtp_port');
$dbUser = Setting::get('smtp_user');
$dbPass = Setting::get('smtp_pass');
$adminEmail = Setting::get('notification_email') ?: Setting::get('store_email') ?: 'test@example.com';

echo "=== Current Database Settings ===\n";
echo "smtp_host: " . ($dbHost ?: '(empty)') . "\n";
echo "smtp_port: " . ($dbPort ?: '(empty)') . "\n";
echo "smtp_user: " . ($dbUser ?: '(empty)') . "\n";
echo "smtp_pass: " . ($dbPass ? '********' : '(empty)') . "\n";
echo "notification_email: " . ($adminEmail ?: '(empty)') . "\n\n";

echo "=== Current .env Mail Config ===\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n\n";

// If DB has host, set runtime config
if (!empty($dbHost)) {
    Config::set('mail.default', 'smtp');
    Config::set('mail.mailers.smtp.host', $dbHost);
    Config::set('mail.mailers.smtp.port', $dbPort ?: 587);
    Config::set('mail.mailers.smtp.username', $dbUser);
    Config::set('mail.mailers.smtp.password', $dbPass);
    Config::set('mail.mailers.smtp.scheme', ((int)$dbPort === 465) ? 'smtps' : null);
    Config::set('mail.from.address', $dbUser ?: 'noreply@example.com');
    Config::set('mail.from.name', Setting::get('store_name', 'EASY POS'));
}

echo "=== Testing SMTP Connection & Delivery ===\n";

$host = config('mail.mailers.smtp.host');
$port = config('mail.mailers.smtp.port');
$user = config('mail.mailers.smtp.username');

if (empty($host) || $host === '127.0.0.1' || empty($user)) {
    echo "❌ Status: SMTP is not yet configured with valid credentials.\n";
    echo "Reason: Host is '$host' and username is '$user'.\n";
    exit(0);
}

try {
    echo "Connecting to $host:$port...\n";
    
    // Check TCP socket connectivity first
    $errno = 0;
    $errstr = '';
    $fp = @fsockopen($host, (int)$port, $errno, $errstr, 10);
    if (!$fp) {
        echo "❌ TCP Connection Failed: Could not connect to $host:$port (Error: $errstr [$errno])\n";
    } else {
        echo "✅ TCP Connection to $host:$port succeeded!\n";
        fclose($fp);
    }

    echo "Attempting to send test email to: $adminEmail...\n";
    Mail::raw("Hello! This is a test email from your POS system sent at " . date('Y-m-d H:i:s') . " to verify SMTP is working.", function ($message) use ($adminEmail) {
        $message->to($adminEmail)
                ->subject('POS System - SMTP Test Email');
    });

    echo "✅ SUCCESS! Test email was successfully sent via SMTP to: $adminEmail\n";
} catch (\Throwable $e) {
    echo "❌ SMTP ERROR: " . $e->getMessage() . "\n";
}
