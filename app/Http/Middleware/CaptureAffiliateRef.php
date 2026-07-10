<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AffiliateLink;

class CaptureAffiliateRef
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1) ambil ref dari query (prioritas)
        $ref = trim((string) $request->query('ref', ''));

        // 2) kalau tidak ada ref di query, coba restore dari cookie bila session kosong
        if ($ref === '' && !$request->session()->has('affiliate_ref')) {
            $cookieRef = trim((string) $request->cookie('affiliate_ref', ''));
            if ($cookieRef !== '') {
                $ref = $cookieRef;
            }
        }

        // 3) kalau sudah dapat ref, resolve ke affiliate_links
        if ($ref !== '') {
            $link = AffiliateLink::where('code', $ref)->first();

            if ($link) {
                // simpan session penting untuk dipakai saat order dibuat
                $request->session()->put('affiliate_ref', $link->code);
                $request->session()->put('affiliate_link_id', $link->id);
                $request->session()->put('affiliate_user_id', $link->user_id);

                // promo/coupon (pakai yang ada di schema lu)
                // schema lu sekarang punya promo_code (baru) dan juga coupon_code (lama)
                if (!empty($link->promo_code)) {
                    $request->session()->put('affiliate_promo_code', $link->promo_code);
                } elseif (!empty($link->coupon_code)) {
                    $request->session()->put('affiliate_promo_code', $link->coupon_code);
                }

                // acquisition (schema lu: platform/platform_id baru, acq_platform/acq_id lama)
                if (!empty($link->platform)) {
                    $request->session()->put('affiliate_platform', $link->platform);
                } elseif (!empty($link->acq_platform)) {
                    $request->session()->put('affiliate_platform', $link->acq_platform);
                }

                if (!empty($link->platform_id)) {
                    $request->session()->put('affiliate_platform_id', $link->platform_id);
                } elseif (!empty($link->acq_id)) {
                    $request->session()->put('affiliate_platform_id', $link->acq_id);
                }

                // UTM (kalau ada di link)
                foreach (['utm_source','utm_medium','utm_campaign','utm_content','utm_term'] as $k) {
                    if (!empty($link->{$k})) {
                        $request->session()->put('affiliate_'.$k, $link->{$k});
                    }
                }

                // anti double count click per session
                if (!$request->session()->has('affiliate_click_counted_'.$link->id)) {
                    $link->increment('clicks');
                    $request->session()->put('affiliate_click_counted_'.$link->id, true);
                }

                // cookie 30 hari
                cookie()->queue(cookie('affiliate_ref', $link->code, 60 * 24 * 30));
            }
        }

        return $next($request);
    }
}
