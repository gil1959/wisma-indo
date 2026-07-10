<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class TravelDocumentController extends Controller
{
    public function index()
    {
        $isEn = app()->getLocale() === 'en';

        $get = function (string $key, string $default = '') use ($isEn) {
            if ($isEn) {
                return Setting::getValue($key . '_en', Setting::getValue($key, $default));
            }
            return Setting::getValue($key, $default);
        };

        $pageTitle = $get('travel_docs_meta_title', $isEn ? 'Travel Documents' : 'Dokumen Perjalanan');
        $metaDesc  = $get('travel_docs_meta_desc', '');

        $heroBadge = $get('travel_docs_hero_badge', $isEn ? 'Document Service' : 'Layanan Dokumen');
        $heroTitle = $get('travel_docs_hero_title', $isEn ? 'Passport & Visa' : 'Pengurusan Paspor & Visa');
        $heroDesc  = $get(
            'travel_docs_hero_desc',
            $isEn
                ? 'Passport and visa processing service: requirements, pricing, and how to order.'
                : 'Layanan pengurusan paspor dan visa: syarat, harga, dan cara pemesanan.'
        );

        $tabPassportTitle = $get('travel_docs_tab_passport_title', $isEn ? 'Passport' : 'Paspor');
        $tabVisaTitle     = $get('travel_docs_tab_visa_title', $isEn ? 'Visa' : 'Visa');

        $passportHtml = $get('travel_docs_passport_html', '');
        $visaHtml     = $get('travel_docs_visa_html', '');

        $passportPriceTitle = $get('travel_docs_passport_price_title', $isEn ? 'Passport Pricing' : 'Harga Paspor');
        $passportPriceHtml  = $get('travel_docs_passport_price_html', '');

        $visaPriceTitle = $get('travel_docs_visa_price_title', $isEn ? 'Visa Pricing' : 'Harga Visa');
        $visaPriceHtml  = $get('travel_docs_visa_price_html', '');

        $immigrationTitle = $get('travel_docs_immigration_title', $isEn ? 'Immigration Info' : 'Info Imigrasi');
        $immigrationHtml  = $get('travel_docs_immigration_html', '');

        $orderTitle = $get('travel_docs_order_title', $isEn ? 'Order Passport/Visa' : 'Pemesanan Paspor/Visa');
        $orderHtml  = $get('travel_docs_order_html', '');
        $orderWa    = Setting::getValue('travel_docs_order_whatsapp', ''); // WA sama untuk semua bahasa

        $downloadTitle = $get('travel_docs_download_title', $isEn ? 'Downloads' : 'Download');
        $downloadsRaw  = $get('travel_docs_downloads', '[]');
        $downloads     = json_decode($downloadsRaw, true);
        $downloads     = is_array($downloads) ? $downloads : [];

        return view('front.pages.travel-documents', compact(
            'pageTitle',
            'metaDesc',
            'heroBadge',
            'heroTitle',
            'heroDesc',
            'tabPassportTitle',
            'tabVisaTitle',
            'passportHtml',
            'visaHtml',
            'passportPriceTitle',
            'passportPriceHtml',
            'visaPriceTitle',
            'visaPriceHtml',
            'immigrationTitle',
            'immigrationHtml',
            'orderTitle',
            'orderHtml',
            'orderWa',
            'downloadTitle',
            'downloads'
        ));
    }
}
