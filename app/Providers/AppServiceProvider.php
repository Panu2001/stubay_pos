<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! class_exists(\Dom\HTMLDocument::class)) {
            Str::macro('sanitizeHtml', function (string $html): string {
                $allowedTags = '<a><abbr><b><br><code><del><em><i><kbd><mark><p><s><small><span><strong><sub><sup><u>';

                return strip_tags($html, $allowedTags);
            });

            Stringable::macro('sanitizeHtml', function (): Stringable {
                return new Stringable(Str::sanitizeHtml($this->value));
            });
        }

        // Force HTTPS when server is running behind HTTPS (works even with cached config)
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        if ($isHttps) {
            URL::forceScheme('https');
            URL::forceRootUrl('https://' . (request()->getHost() ?: parse_url(config('app.url'), PHP_URL_HOST)));
        }

        \Illuminate\Support\Facades\Event::listen(function (\Illuminate\Auth\Events\Login $event) {
            if ($event->user instanceof \App\Models\User) {
                \App\Models\LoginHistory::create([
                    'user_id' => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'login_at' => now(),
                ]);

                \App\Services\ShiftService::startForUser($event->user);
            }
        });

        \Illuminate\Support\Facades\Event::listen(function (\Illuminate\Auth\Events\Logout $event) {
            if ($event->user instanceof \App\Models\User) {
                \App\Services\ShiftService::closeForUser($event->user);
            }
        });

        // Dynamic Mail Configuration from Database Settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $smtpHost = \App\Models\Setting::get('smtp_host');
                if (!empty($smtpHost)) {
                    $smtpPort = (int) \App\Models\Setting::get('smtp_port', 587);
                    $smtpUser = \App\Models\Setting::get('smtp_user');
                    $smtpPass = \App\Models\Setting::get('smtp_pass');
                    $storeName = \App\Models\Setting::get('store_name', config('app.name', 'EASY POS'));
                    $fromEmail = \App\Models\Setting::get('store_email') ?: \App\Models\Setting::get('notification_email') ?: $smtpUser;

                    config([
                        'mail.default' => 'smtp',
                        'mail.mailers.smtp.host' => $smtpHost,
                        'mail.mailers.smtp.port' => $smtpPort,
                        'mail.mailers.smtp.username' => $smtpUser,
                        'mail.mailers.smtp.password' => $smtpPass,
                        'mail.mailers.smtp.scheme' => ($smtpPort === 465) ? 'smtps' : null,
                        'mail.from.address' => $fromEmail ?: config('mail.from.address'),
                        'mail.from.name' => $storeName,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully if database is not reachable
        }
    }
}
