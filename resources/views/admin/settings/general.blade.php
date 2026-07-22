@extends('layouts.admin')
@section('title', 'General Settings')
@section('page-title', 'General Settings')
@section('content')

@if(session('success'))
<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 font-bold">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900 font-bold">
    <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div x-data="{ tab: 'identitas' }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Tabs Header -->
    <div class="flex flex-wrap border-b border-slate-200 bg-slate-50">
        <button @click="tab = 'identitas'" :class="tab === 'identitas' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Identitas Web</button>
        <button @click="tab = 'seo'" :class="tab === 'seo' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">SEO Global</button>
        <button @click="tab = 'kontak'" :class="tab === 'kontak' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Kontak & Footer</button>
        <button @click="tab = 'pembayaran'" :class="tab === 'pembayaran' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Pembayaran</button>
        <button @click="tab = 'integrasi'" :class="tab === 'integrasi' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Integrasi</button>
    </div>

    <!-- MAIN FORM FOR GENERAL SETTINGS -->
    <form method="POST" action="{{ route('admin.settings.general.save') }}" enctype="multipart/form-data">
        @csrf

        <!-- TAB 1: IDENTITAS WEB -->
        <div x-show="tab === 'identitas'" class="p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Pengaturan Identitas Website</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Brand</label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', $settings['brand_name'] ?? 'Rumaindo') }}" class="w-full rounded-xl border-slate-300 focus:border-[#0194F3]">
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Logo Website</label>
                    @if(isset($settings['site_logo']))
                        <img src="{{ asset($settings['site_logo']) }}" class="h-16 mb-4 border rounded p-1 bg-white">
                    @endif
                    <input type="file" name="site_logo" class="w-full text-sm">
                    <p class="text-xs text-slate-500 mt-2">Logo untuk Navbar, Footer, dan Login.</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Favicon (Icon Tab)</label>
                    @if(isset($settings['site_favicon']))
                        <img src="{{ asset($settings['site_favicon']) }}" class="h-8 mb-4 border rounded p-1 bg-white">
                    @endif
                    <input type="file" name="site_favicon" class="w-full text-sm">
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600">Simpan Identitas</button>
            </div>
        </div>

        <!-- TAB 2: SEO GLOBAL -->
        <div x-show="tab === 'seo'" x-cloak class="p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Pengaturan SEO Global</h3>
            <div class="space-y-6 max-w-4xl">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Meta Title</label>
                    <input type="text" name="seo_meta_title" value="{{ old('seo_meta_title', $settings['seo_meta_title'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Meta Description</label>
                    <textarea name="seo_meta_desc" rows="3" class="w-full rounded-xl border-slate-300">{{ old('seo_meta_desc', $settings['seo_meta_desc'] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Meta Keywords</label>
                    <input type="text" name="seo_meta_keywords" value="{{ old('seo_meta_keywords', $settings['seo_meta_keywords'] ?? '') }}" class="w-full rounded-xl border-slate-300" placeholder="properti, rumah, apartemen">
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600">Simpan SEO</button>
            </div>
        </div>

        <!-- TAB 3: KONTAK & FOOTER (Main Settings) -->
        <div x-show="tab === 'kontak'" x-cloak class="p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Pengaturan Kontak & Footer</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tagline Footer</label>
                    <textarea name="footer_tagline" rows="2" class="w-full rounded-xl border-slate-300">{{ old('footer_tagline', $settings['footer_tagline'] ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                    <textarea name="footer_address" rows="2" class="w-full rounded-xl border-slate-300">{{ old('footer_address', $settings['footer_address'] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Telepon</label>
                    <input type="text" name="footer_phone" value="{{ old('footer_phone', $settings['footer_phone'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">WhatsApp</label>
                    <input type="text" name="footer_whatsapp" value="{{ old('footer_whatsapp', $settings['footer_whatsapp'] ?? '') }}" class="w-full rounded-xl border-slate-300" placeholder="628xxx">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" name="footer_email" value="{{ old('footer_email', $settings['footer_email'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Copyright Text</label>
                    <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Admin (Notifikasi)</label>
                    <input type="email" name="admin_notification_email" value="{{ old('admin_notification_email', $settings['admin_notification_email'] ?? '') }}" class="w-full rounded-xl border-slate-300" placeholder="Contoh: admin@wismaindo.com">
                    <p class="text-xs text-slate-500 mt-1">Digunakan untuk menerima notifikasi register & pembayaran baru.</p>
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600">Simpan Kontak</button>
            </div>
        </div>

        <!-- TAB: INTEGRASI -->
        <div x-show="tab === 'integrasi'" x-cloak class="p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Pengaturan Integrasi Pihak Ketiga</h3>
            <p class="text-sm text-slate-500 mb-6">Atur pengaturan login sosial dan integrasi lainnya di sini.</p>
            
            <div class="grid grid-cols-1 gap-6 max-w-4xl">
                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-700">Login dengan Google</h4>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="google_login_active" value="0">
                            <input type="checkbox" name="google_login_active" value="1" {{ (old('google_login_active', $settings['google_login_active'] ?? '0') == '1') ? 'checked' : '' }} class="w-5 h-5 rounded text-[#0194F3]">
                            <span class="text-sm font-bold text-slate-700">Aktifkan Google Login</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Google Client ID</label>
                        <input type="text" name="google_client_id" value="{{ old('google_client_id', $settings['google_client_id'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Google Client Secret</label>
                        <input type="password" name="google_client_secret" value="{{ old('google_client_secret', $settings['google_client_secret'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div class="mt-2 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <p class="text-sm font-bold text-blue-800 mb-1"><i data-lucide="info" class="w-4 h-4 inline-block mr-1"></i> Authorized Redirect URI</p>
                        <p class="text-xs text-blue-700 leading-relaxed mb-2">Salin dan tempel URL ini ke kolom <strong>Authorized redirect URIs</strong> pada Google Cloud Console Anda:</p>
                        <code class="block bg-white p-2 text-xs font-mono text-blue-600 rounded border border-blue-200 break-all">{{ url('/auth/google/callback') }}</code>
                    </div>
                </div>
                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-700">Google Maps API (Geolokasi)</h4>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Google Maps API Key</label>
                        <input type="text" name="google_maps_api_key" value="{{ old('google_maps_api_key', $settings['google_maps_api_key'] ?? '') }}" class="w-full rounded-xl border-slate-300" placeholder="AIzaSy...">
                        <p class="text-xs text-slate-500 mt-1">Digunakan untuk fitur Autocomplete Lokasi dan Visual Peta saat pasang iklan & pencarian.</p>
                    </div>
                </div>
                
                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-700">Google Gemini AI Studio (Deskripsi Otomatis)</h4>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Gemini API Key</label>
                        <input type="text" name="gemini_api_key" value="{{ old('gemini_api_key', $settings['gemini_api_key'] ?? '') }}" class="w-full rounded-xl border-slate-300" placeholder="AIzaSy...">
                        <p class="text-xs text-slate-500 mt-1">Digunakan untuk fitur Generate Deskripsi Iklan dan Artikel secara otomatis menggunakan Google Gemini AI.</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600">Simpan Integrasi</button>
            </div>
        </div>

        <!-- TAB 4: PEMBAYARAN -->
        <div x-show="tab === 'pembayaran'" x-cloak class="p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Integrasi Payment Gateway</h3>
            <p class="text-sm text-slate-500 mb-6">Masukkan kredensial Payment Gateway Anda di sini. Jika diisi, list bank/e-wallet akan ditarik secara otomatis saat pengguna melakukan Checkout Top Up.</p>
            
            <div class="grid grid-cols-1 gap-6 max-w-4xl">
                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-700">Kredensial Tripay</h4>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="tripay_active" value="0">
                            <input type="checkbox" name="tripay_active" value="1" {{ (old('tripay_active', $settings['tripay_active'] ?? '0') == '1') ? 'checked' : '' }} class="w-5 h-5 rounded text-[#0194F3]">
                            <span class="text-sm font-bold text-slate-700">Aktifkan Tripay</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Environment Mode</label>
                        <select name="tripay_mode" class="w-full rounded-xl border-slate-300">
                            <option value="sandbox" {{ (old('tripay_mode', $settings['tripay_mode'] ?? 'sandbox') == 'sandbox') ? 'selected' : '' }}>Sandbox / Testing</option>
                            <option value="production" {{ (old('tripay_mode', $settings['tripay_mode'] ?? 'sandbox') == 'production') ? 'selected' : '' }}>Production / Live</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Merchant Code</label>
                        <input type="text" name="tripay_merchant_code" value="{{ old('tripay_merchant_code', $settings['tripay_merchant_code'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">API Key</label>
                        <input type="password" name="tripay_api_key" value="{{ old('tripay_api_key', $settings['tripay_api_key'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Private Key</label>
                        <input type="password" name="tripay_private_key" value="{{ old('tripay_private_key', $settings['tripay_private_key'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                    </div>
                    <div class="mt-2 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <p class="text-sm font-bold text-blue-800 mb-1"><i data-lucide="info" class="w-4 h-4 inline-block mr-1"></i> Informasi Webhook Tripay</p>
                        <p class="text-xs text-blue-700 leading-relaxed mb-2">Agar sistem Anda bisa menerima konfirmasi pembayaran secara otomatis (Top Up sukses), masukkan URL berikut ke bagian <strong>Callback URL</strong> di dalam menu Merchant > URL Callback pada dashboard Tripay Anda:</p>
                        <code class="block bg-white p-2 text-xs font-mono text-blue-600 rounded border border-blue-200 break-all">{{ url('/api/webhooks/topup/tripay') }}</code>
                    </div>
                </div>

                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-700">Kredensial Xendit</h4>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="xendit_active" value="0">
                            <input type="checkbox" name="xendit_active" value="1" {{ (old('xendit_active', $settings['xendit_active'] ?? '0') == '1') ? 'checked' : '' }} class="w-5 h-5 rounded text-[#0194F3]">
                            <span class="text-sm font-bold text-slate-700">Aktifkan Xendit</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Secret API Key Xendit</label>
                        <input type="password" name="xendit_api_key" value="{{ old('xendit_api_key', $settings['xendit_api_key'] ?? '') }}" class="w-full rounded-xl border-slate-300" placeholder="xnd_production_... / xnd_development_...">
                        <p class="text-xs text-slate-500 mt-1">Sistem otomatis mendeteksi Sandbox/Live dari prefix API Key Anda.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Callback Verification Token (Webhook)</label>
                        <input type="password" name="xendit_callback_token" value="{{ old('xendit_callback_token', $settings['xendit_callback_token'] ?? '') }}" class="w-full rounded-xl border-slate-300">
                        <p class="text-xs text-slate-500 mt-1">Dapatkan token ini di menu Callbacks pada dashboard Xendit untuk meningkatkan keamanan verifikasi Webhook.</p>
                    </div>
                    <div class="mt-2 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <p class="text-sm font-bold text-blue-800 mb-1"><i data-lucide="info" class="w-4 h-4 inline-block mr-1"></i> Informasi Webhook Xendit</p>
                        <p class="text-xs text-blue-700 leading-relaxed mb-2">Buka Dashboard Xendit Anda, masuk ke menu <strong>Settings > Callbacks / Webhooks</strong>. Pada bagian <strong>Invoices Paid</strong>, masukkan URL berikut:</p>
                        <code class="block bg-white p-2 text-xs font-mono text-blue-600 rounded border border-blue-200 break-all">{{ url('/api/webhooks/topup/xendit') }}</code>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600">Simpan Kredensial PG</button>
            </div>
        </div>
    </form>

    <!-- TAB 3: LOGO FOOTER (Outside Main Form because of nested forms) -->
    <div x-show="tab === 'kontak'" x-cloak class="p-6 border-t-4 border-slate-100 bg-slate-50/50">
        <h3 class="text-lg font-extrabold text-slate-800 mb-2">Logo Footer (Iklan/Partner)</h3>
        <p class="text-sm text-slate-500 mb-6">Tambahkan logo di footer seperti partner, IATA, atau logo App Store/Play Store. Bisa diurutkan pakai Sort Order.</p>

        <!-- Form Tambah Logo -->
        <form action="{{ route('admin.settings.footer_logo.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-5 rounded-xl border border-slate-200 mb-8 shadow-sm">
            @csrf
            <h4 class="font-bold text-sm text-slate-800 mb-4 border-b border-slate-100 pb-2">Tambah Logo Baru</h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nama Logo</label>
                    <input type="text" name="name" placeholder="Contoh: IATA" required class="w-full rounded-xl border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Logo (PNG/JPG/SVG)</label>
                    <input type="file" name="image" required class="w-full text-sm bg-white border border-slate-300 rounded-xl px-2 py-1">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">URL (Opsional)</label>
                    <input type="url" name="url" placeholder="https://" class="w-full rounded-xl border-slate-300 text-sm">
                </div>
                <div class="flex gap-2 items-center">
                    <div class="w-20">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Order</label>
                        <input type="number" name="order" value="0" class="w-full rounded-xl border-slate-300 text-sm">
                    </div>
                    <div class="pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded text-[#0194F3]">
                            <span class="text-sm font-semibold text-slate-700">Aktif</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="bg-[#0194F3] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-600 text-sm shadow-sm">Tambah Logo</button>
            </div>
        </form>

        <h4 class="font-bold text-sm text-slate-800 mb-4 border-b border-slate-200 pb-2">Daftar Logo Footer</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-3 text-xs font-bold text-slate-500">Preview</th>
                        <th class="p-3 text-xs font-bold text-slate-500">Nama</th>
                        <th class="p-3 text-xs font-bold text-slate-500">URL</th>
                        <th class="p-3 text-xs font-bold text-slate-500 text-center">Order</th>
                        <th class="p-3 text-xs font-bold text-slate-500 text-center">Status</th>
                        <th class="p-3 text-xs font-bold text-slate-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($footerLogos as $logo)
                    <tr>
                        <td class="p-3">
                            <div class="bg-slate-900 rounded-lg p-2 inline-block">
                                <img src="{{ asset($logo->image) }}" class="h-8 max-w-[120px] object-contain">
                            </div>
                        </td>
                        <td class="p-3 font-semibold text-sm">{{ $logo->name }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ $logo->url ?: '-' }}</td>
                        <td class="p-3 text-center text-sm font-medium">{{ $logo->order }}</td>
                        <td class="p-3 text-center">
                            @if($logo->is_active)
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">Aktif</span>
                            @else
                                <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded-full text-xs font-bold">Draft</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.settings.footer_logo.destroy', $logo->id) }}" method="POST" onsubmit="return confirm('Hapus logo ini?');">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"><i data-lucide="trash" class="w-4 h-4"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-sm text-slate-500">Belum ada logo footer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 4: OFFLINE PAYMENT (Outside Main Form) -->
    <div x-show="tab === 'pembayaran'" x-cloak class="p-6 border-t-4 border-slate-100 bg-slate-50/50">
        <h3 class="text-lg font-extrabold text-slate-800 mb-2">Pengaturan Pembayaran Offline</h3>
        <p class="text-sm text-slate-500 mb-6">Atur rekening manual dan konfigurasi kode unik pembayaran.</p>

        <!-- Form Kode Unik -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 mb-8 shadow-sm">
            <h4 class="font-bold text-sm text-slate-800 mb-2">Kode Unik Transfer Manual</h4>
            <p class="text-xs text-slate-500 mb-4">Total transfer = total invoice + kode unik (contoh: 1.500.000 &rarr; 1.500.198).</p>
            <form action="{{ route('admin.settings.general.save') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Min</label>
                        <input type="number" name="offline_unique_code_min" value="{{ old('offline_unique_code_min', $settings['offline_unique_code_min'] ?? '1') }}" class="w-full rounded-xl border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Max</label>
                        <input type="number" name="offline_unique_code_max" value="{{ old('offline_unique_code_max', $settings['offline_unique_code_max'] ?? '999') }}" class="w-full rounded-xl border-slate-300 text-sm">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#0194F3] text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-600 text-sm shadow-sm">Simpan Kode Unik</button>
                </div>
            </form>
        </div>

        <h3 class="text-lg font-extrabold text-slate-800 mb-2 mt-8">Bank Transfer Manual</h3>
        <!-- Form Tambah Rekening -->
        <form action="{{ route('admin.offline-payment-methods.store') }}" method="POST" class="bg-white p-5 rounded-xl border border-slate-200 mb-8 shadow-sm">
            @csrf
            <h4 class="font-bold text-sm text-slate-800 mb-4 border-b border-slate-100 pb-2">Tambah Rekening Baru</h4>
            <p class="text-xs text-slate-500 mb-4">Isi data rekening bank untuk pembayaran manual.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nama Bank</label>
                    <input type="text" name="name" required class="w-full rounded-xl border-slate-300 text-sm" placeholder="Contoh: BCA">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nomor Rekening</label>
                    <input type="text" name="account_number" required class="w-full rounded-xl border-slate-300 text-sm" placeholder="Contoh: 1234567890">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Atas Nama</label>
                    <input type="text" name="account_name" required class="w-full rounded-xl border-slate-300 text-sm" placeholder="Contoh: Wisma Indo">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">SWIFT Code (Opsional)</label>
                    <input type="text" name="swift_code" class="w-full rounded-xl border-slate-300 text-sm" placeholder="Contoh: BMRIIIDJAXXX">
                    <p class="text-[10px] text-slate-400 mt-1">Dipakai untuk transfer internasional (wire transfer).</p>
                </div>
            </div>
            <div class="flex gap-4 items-center pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-[#0194F3]">
                    <span class="text-sm font-semibold text-slate-700">Aktif</span>
                </label>
                <button type="submit" class="bg-[#0194F3] text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-600 text-sm shadow-sm flex-grow">Tambah Rekening</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-3 text-xs font-bold text-slate-500">Bank</th>
                        <th class="p-3 text-xs font-bold text-slate-500">Nomor Rekening</th>
                        <th class="p-3 text-xs font-bold text-slate-500">Atas Nama</th>
                        <th class="p-3 text-xs font-bold text-slate-500">SWIFT</th>
                        <th class="p-3 text-xs font-bold text-slate-500 text-center">Status</th>
                        <th class="p-3 text-xs font-bold text-slate-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($offlineMethods as $method)
                    <tr>
                        <td class="p-3 font-bold text-slate-800">{{ $method->name }}</td>
                        <td class="p-3 font-mono text-sm">{{ $method->account_number }}</td>
                        <td class="p-3 text-sm">{{ $method->account_name }}</td>
                        <td class="p-3 text-sm font-mono text-slate-500">{{ $method->swift_code ?? '-' }}</td>
                        <td class="p-3 text-center">
                            @if($method->is_active)
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">Aktif</span>
                            @else
                                <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded-full text-xs font-bold">Draft</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.offline-payment-methods.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?');">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"><i data-lucide="trash" class="w-4 h-4"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-sm text-slate-500">Belum ada rekening yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
