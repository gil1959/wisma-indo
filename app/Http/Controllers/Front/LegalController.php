<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class LegalController extends Controller
{
    public function privacy()
    {
        $isEn = app()->getLocale() === 'en';

        $defaultTitle = $isEn ? 'Privacy Policy' : 'Kebijakan Privasi';
        $defaultHtml  = $isEn
            ? view('front.pages.legal-defaults.privacy-en')->render()
            : view('front.pages.legal-defaults.privacy')->render();

        $title = $isEn
            ? (Setting::getValue('legal_privacy_title_en') ?: Setting::getValue('legal_privacy_title', $defaultTitle))
            : Setting::getValue('legal_privacy_title', $defaultTitle);

        $html  = $isEn
            ? (Setting::getValue('legal_privacy_html_en') ?: Setting::getValue('legal_privacy_html', $defaultHtml))
            : Setting::getValue('legal_privacy_html', $defaultHtml);

        return view('front.pages.privacy-policy', compact('title', 'html'));
    }

    public function terms()
    {
        $isEn = app()->getLocale() === 'en';

        $defaultTitle = $isEn ? 'Terms & Conditions' : 'Syarat & Ketentuan';
        $defaultHtml  = $isEn
            ? view('front.pages.legal-defaults.terms-en')->render()
            : view('front.pages.legal-defaults.terms')->render();

        $title = $isEn
            ? (Setting::getValue('legal_terms_title_en') ?: Setting::getValue('legal_terms_title', $defaultTitle))
            : Setting::getValue('legal_terms_title', $defaultTitle);

        $html  = $isEn
            ? (Setting::getValue('legal_terms_html_en') ?: Setting::getValue('legal_terms_html', $defaultHtml))
            : Setting::getValue('legal_terms_html', $defaultHtml);

        return view('front.pages.terms-conditions', compact('title', 'html'));
    }

    public function contact()
    {
        $isEn = app()->getLocale() === 'en';

        $defaultTitle = $isEn ? 'Contact' : 'Contact';
        $defaultHtml  = $isEn
            ? view('front.pages.legal-defaults.contact-en')->render()
            : view('front.pages.legal-defaults.contact')->render();

        $title = $isEn
            ? (Setting::getValue('legal_contact_title_en') ?: Setting::getValue('legal_contact_title', $defaultTitle))
            : Setting::getValue('legal_contact_title', $defaultTitle);

        $html  = $isEn
            ? (Setting::getValue('legal_contact_html_en') ?: Setting::getValue('legal_contact_html', $defaultHtml))
            : Setting::getValue('legal_contact_html', $defaultHtml);

        return view('front.pages.contact', compact('title', 'html'));
    }
}
