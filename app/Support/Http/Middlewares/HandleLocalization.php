<?php

namespace App\Support\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class HandleLocalization
{
    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en', 'ar'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale', 'ar'); // Default locale

        if ($request->hasHeader('Accept-Language')) {
            try {
                $acceptLanguage = $request->header('Accept-Language');

                $parsedLocale = $this->extractLocale($acceptLanguage);

                if (in_array($parsedLocale, $this->supportedLocales, true)) {
                    $locale = $parsedLocale;
                }
            } catch (\Exception $e) {
                \Log::debug('Invalid Accept-Language header', [
                    'header' => $acceptLanguage ?? 'N/A',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Extract locale from Accept-Language header
     */
    protected function extractLocale(string $header): ?string
    {
        if (Str::startsWith($header, 'ar')) {
            return 'ar';
        }

        if (Str::startsWith($header, 'en')) {
            return 'en';
        }

        return null;
    }
}
