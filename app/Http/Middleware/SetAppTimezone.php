<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAppTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, \Closure $next): \Symfony\Component\HttpFoundation\Response
    {
        // Skip during installation or if tables are missing
        if ($request->is('install*') || !\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return $next($request);
        }

        try {
            $timezone = \App\Models\Setting::get('timezone', 'auto');
        } catch (\Exception $e) {
            return $next($request);
        }

        if ($timezone === 'auto') {
            $cacheKey = 'timezone_' . str_replace([':', '.'], '_', $request->ip());
            $timezone = cache()->remember($cacheKey, now()->addDay(), function () use ($request) {
                $ip = $request->ip();
                // Skip local IPs
                if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.')) {
                    return 'Asia/Colombo'; // Default to a reasonable fallback or UTC
                }

                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                    if ($response->successful() && isset($response['timezone'])) {
                        return $response['timezone'];
                    }
                } catch (\Exception $e) {
                    // Fail silently
                }

                return config('app.timezone', 'UTC');
            });
        }

        if ($timezone && $timezone !== 'auto') {
            date_default_timezone_set($timezone);
            config(['app.timezone' => $timezone]);
            // Also set for Carbon if needed, but date_default_timezone_set usually handles it
        }

        return $next($request);
    }
}
