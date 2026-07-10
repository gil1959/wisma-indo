<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
   public function handle(Request $request, Closure $next)
{
    $available = array_keys(config('app.available_locales', []));

    // 1) manual override via ?lang=en|id|zh|ja|fr
    $queryLang = strtolower((string)$request->query('lang', ''));
    if ($queryLang && in_array($queryLang, $available, true)) {
        app()->setLocale($queryLang);
        session(['locale' => $queryLang]);
        cookie()->queue(cookie('locale', $queryLang, 60 * 24 * 30));
        return $next($request);
    }

    // 2) cookie override
    $cookieLang = strtolower((string)$request->cookie('locale', ''));
    if ($cookieLang && in_array($cookieLang, $available, true)) {
        app()->setLocale($cookieLang);
        session(['locale' => $cookieLang]);
        return $next($request);
    }

    // 3) existing session behavior
    $sessionLang = strtolower((string)session('locale', ''));
    if ($sessionLang && in_array($sessionLang, $available, true)) {
        app()->setLocale($sessionLang);
        return $next($request);
    }

    // 4) auto-detect: ID => id, selain itu => en
    $country = $this->detectCountryCode($request);
    $autoLocale = ($country === 'ID') ? 'id' : 'en';

    app()->setLocale($autoLocale);
    session(['locale' => $autoLocale]);
    cookie()->queue(cookie('locale', $autoLocale, 60 * 24 * 7));

    return $next($request);
}

private function detectCountryCode(Request $request): ?string
{
    // kalau pakai Cloudflare, ini paling akurat
    $cc = strtoupper(trim((string)$request->header('CF-IPCountry', '')));
    if ($cc && strlen($cc) === 2) return $cc;

    // fallback terakhir: Accept-Language
    $accept = strtolower((string)$request->header('Accept-Language', ''));
    if (str_contains($accept, 'id') || str_contains($accept, 'in')) return 'ID';

    return null;
}

}
