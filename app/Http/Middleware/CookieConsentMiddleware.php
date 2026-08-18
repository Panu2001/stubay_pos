<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CookieConsentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('GET') || $request->isXmlHttpRequest() || $request->hasHeader('X-Livewire')) {
            return $response;
        }

        if (!Setting::get('cookie_enabled', false)) {
            return $response;
        }

        if ($request->cookie('cookie_consent')) {
            return $response;
        }

        $content = $response->getContent();
        
        // Simple banner injection before </body>
        $bannerHtml = $this->getBannerHtml();
        $pos = strripos($content, '</body>');

        if ($pos !== false) {
            $content = substr($content, 0, $pos) . $bannerHtml . substr($content, $pos);
            $response->setContent($content);
        }

        return $response;
    }

    protected function getBannerHtml(): string
    {
        $message = Setting::get('cookie_message', 'We use cookies to improve your experience.');
        $buttonText = Setting::get('cookie_button_text', 'Accept');

        return <<<HTML
        <div id="cookie-consent-banner" style="position: fixed; bottom: 20px; left: 20px; right: 20px; background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; z-index: 9999; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; gap: 20px; max-width: 800px; margin: 0 auto;">
            <div style="font-size: 14px; color: #475569; font-family: sans-serif;">
                {$message}
            </div>
            <button onclick="acceptCookies()" style="background: #10b981; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                {$buttonText}
            </button>
        </div>
        <script>
            function acceptCookies() {
                document.cookie = "cookie_consent=1; path=/; max-age=" + (365 * 24 * 60 * 60);
                document.getElementById('cookie-consent-banner').style.display = 'none';
            }
        </script>
HTML;
    }
}
