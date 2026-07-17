<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function privacy()
    {
        $isEn = app()->getLocale() === 'en';
        $defaultTitle = $isEn ? 'Privacy Policy' : 'Kebijakan Privasi';
        
        $title = $isEn 
            ? (Setting::getValue('legal_privacy_title_en') ?: Setting::getValue('legal_privacy_title', $defaultTitle))
            : Setting::getValue('legal_privacy_title', $defaultTitle);
            
        $content = $isEn 
            ? (Setting::getValue('legal_privacy_html_en') ?: Setting::getValue('legal_privacy_html', ''))
            : Setting::getValue('legal_privacy_html', '');

        return view('admin.legal.privacy', compact('title', 'content'));
    }

    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $isEn = app()->getLocale() === 'en';
        
        if ($isEn) {
            Setting::setValue('legal_privacy_title_en', $request->title);
            Setting::setValue('legal_privacy_html_en', $request->content);
        } else {
            Setting::setValue('legal_privacy_title', $request->title);
            Setting::setValue('legal_privacy_html', $request->content);
        }

        return redirect()->route('admin.legal.privacy')->with('success', 'Kebijakan Privasi berhasil diperbarui.');
    }

    public function terms()
    {
        $isEn = app()->getLocale() === 'en';
        $defaultTitle = $isEn ? 'Terms & Conditions' : 'Syarat & Ketentuan';
        
        $title = $isEn 
            ? (Setting::getValue('legal_terms_title_en') ?: Setting::getValue('legal_terms_title', $defaultTitle))
            : Setting::getValue('legal_terms_title', $defaultTitle);
            
        $content = $isEn 
            ? (Setting::getValue('legal_terms_html_en') ?: Setting::getValue('legal_terms_html', ''))
            : Setting::getValue('legal_terms_html', '');

        return view('admin.legal.terms', compact('title', 'content'));
    }

    public function updateTerms(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $isEn = app()->getLocale() === 'en';
        
        if ($isEn) {
            Setting::setValue('legal_terms_title_en', $request->title);
            Setting::setValue('legal_terms_html_en', $request->content);
        } else {
            Setting::setValue('legal_terms_title', $request->title);
            Setting::setValue('legal_terms_html', $request->content);
        }

        return redirect()->route('admin.legal.terms')->with('success', 'Syarat & Ketentuan berhasil diperbarui.');
    }

    public function contact()
    {
        $isEn = app()->getLocale() === 'en';
        $defaultTitle = $isEn ? 'Contact' : 'Kontak Kami';
        
        $title = $isEn 
            ? (Setting::getValue('legal_contact_title_en') ?: Setting::getValue('legal_contact_title', $defaultTitle))
            : Setting::getValue('legal_contact_title', $defaultTitle);
            
        $content = $isEn 
            ? (Setting::getValue('legal_contact_html_en') ?: Setting::getValue('legal_contact_html', ''))
            : Setting::getValue('legal_contact_html', '');

        return view('admin.legal.contact', compact('title', 'content'));
    }

    public function updateContact(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $isEn = app()->getLocale() === 'en';
        
        if ($isEn) {
            Setting::setValue('legal_contact_title_en', $request->title);
            Setting::setValue('legal_contact_html_en', $request->content);
        } else {
            Setting::setValue('legal_contact_title', $request->title);
            Setting::setValue('legal_contact_html', $request->content);
        }

        return redirect()->route('admin.legal.contact')->with('success', 'Halaman Kontak berhasil diperbarui.');
    }
}
