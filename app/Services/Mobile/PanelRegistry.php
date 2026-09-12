<?php
namespace App\Services\Mobile;

/** Explicit native screen registry. Only these audited web operations are exposed. */
class PanelRegistry
{
    public static function modules(string $area): array
    {
        if ($area === 'partner') return [
            'statistics'=>['title'=>'Dashboard Partner','controller'=>'User\\PartnerController','read'=>'statistics','premium'=>true],
            'leads'=>['title'=>'Lead Pembeli','controller'=>'User\\PartnerController','read'=>'leads','detail'=>'showLead','model'=>'BuyerLead','premium'=>true,'actions'=>['activity'=>['method'=>'addLeadActivity','title'=>'Catat aktivitas','record'=>true]]],
            'surveys'=>['title'=>'Jadwal Survey','controller'=>'User\\PartnerController','read'=>'surveys','model'=>'SurveySchedule','premium'=>true,'actions'=>['status'=>['method'=>'updateSurveyStatus','title'=>'Ubah status','record'=>true]]],
            'billing'=>['title'=>'Tagihan Bulanan','controller'=>'Api\\V1\\NativePartnerBillingController','read'=>'index','model'=>'PartnerSubscription','owner_key'=>'user_id','actions'=>['purchase'=>['method'=>'purchase','title'=>'Beli paket partner'],'proof'=>['method'=>'proof','title'=>'Unggah bukti transfer','record'=>true]]],
            'whatsapp'=>['title'=>'Pengaturan WhatsApp','controller'=>'User\\PartnerController','read'=>'whatsapp','premium'=>true,'form'=>true,'actions'=>['save'=>['method'=>'updateWhatsapp','title'=>'Simpan template']]],
            'profile'=>['title'=>'Profil Partner','controller'=>'Partner\\ProfileController','read'=>'edit','form'=>true,'actions'=>['save'=>['method'=>'update','title'=>'Simpan profil'],'password'=>['method'=>'updatePassword','title'=>'Ubah password']]],
            'kpr'=>['title'=>'Pengajuan KPR','premium'=>true],
        ];
        $modules=[];
        foreach ([
            'users'=>['Pengguna','User','User'], 'pages'=>['Halaman CMS','Page','Page'],
            'listing-categories'=>['Kategori Iklan','ListingCategory','ListingCategory'],
            'article-categories'=>['Kategori Artikel','ArticleCategory','ArticleCategory'],
            'articles'=>['Artikel','Article','Article'], 'topup-packages'=>['Paket Kuota','TopupPackage','TopupPackage'],
            'partner-packages'=>['Paket Partner','PartnerPackage','PartnerPackage'], 'listing-packages'=>['Paket Promosi Iklan','ListingPackage','ListingPackage'],
            'listings'=>['Iklan','Listing','Listing'],
        ] as $key=>$row) {
            $modules[$key]=['title'=>$row[0],'controller'=>'Admin\\'.$row[1].'Controller','model'=>$row[2],'read'=>'index','actions'=>[
                'create'=>['method'=>'store','title'=>'Tambah'], 'edit'=>['method'=>'update','title'=>'Edit','record'=>true], 'delete'=>['method'=>'destroy','title'=>'Hapus','record'=>true,'confirm'=>true]
            ]];
        }
        $modules['listings']['actions']['status']=['method'=>'update','title'=>'Ubah status','record'=>true,'confirm'=>true];
        foreach (['toggleFreeQuota'=>'Ubah kuota gratis','addQuota'=>'Tambah kuota','subtractQuota'=>'Kurangi kuota'] as $key=>$title) $modules['users']['actions'][$key]=['method'=>$key,'title'=>$title,'record'=>true,'confirm'=>true];
        $modules['users']['actions']['impersonate']=['method'=>'impersonate','title'=>'Masuk sebagai pengguna','record'=>true,'confirm'=>true];
        $modules['users']['actions']['toggleGlobalFreeQuota']=['method'=>'toggleGlobalFreeQuota','title'=>'Ubah kuota gratis pendaftaran','confirm'=>true];
        $modules['users']['actions']['updatePopup']=['method'=>'updatePopup','title'=>'Pesan popup pengguna'];
        $modules['partner-packages']['actions']['updateFree']=['method'=>'updateFree','title'=>'Atur paket gratis'];
        $modules['dashboard']=['title'=>'Dashboard Admin','controller'=>'Admin\\DashboardController','read'=>'index'];
        $modules['referrals']=['title'=>'Rekomendasi Lead','controller'=>'Admin\\ReferralLeadController','read'=>'index','model'=>'BuyerLead','actions'=>['create'=>['method'=>'store','title'=>'Tambah rekomendasi']]];
        $modules['notifications']=['title'=>'Kirim Notifikasi','controller'=>'Admin\\NotificationController','read'=>'create','form'=>true,'actions'=>['send'=>['method'=>'store','title'=>'Kirim notifikasi','confirm'=>true]]];
        foreach ([
            'partner-registrations'=>['Pendaftaran Partner','PartnerRegistration','PartnerRegistration'],
            'topups'=>['Transaksi Kuota','Topup','TopupTransaction'],
            'partner-subscriptions'=>['Langganan Partner','PartnerSubscription','PartnerSubscription'],
            'listing-promotions'=>['Transaksi Promosi','ListingPromotion','ListingTransaction'],
        ] as $key=>$row) {
            $actions=[];
            if (in_array($key,['partner-subscriptions','listing-promotions','topups'])) $actions['edit']=['method'=>'update','title'=>'Ubah status','record'=>true,'confirm'=>true];
            else foreach (['approve'=>'Setujui','reject'=>'Tolak'] as $action=>$title) $actions[$action]=['method'=>$action,'title'=>$title,'record'=>true,'confirm'=>true];
            $actions['delete']=['method'=>'destroy','title'=>'Hapus','record'=>true,'confirm'=>true];
            $modules[$key]=['title'=>$row[0],'controller'=>'Admin\\'.$row[1].'Controller','model'=>$row[2],'read'=>'index','actions'=>$actions];
        }
        foreach(['suspend'=>'Tangguhkan','unsuspend'=>'Aktifkan kembali'] as $key=>$title) $modules['partner-registrations']['actions'][$key]=['method'=>$key,'title'=>$title,'record'=>true,'confirm'=>true];
        $modules['reports']=['title'=>'Laporan Transaksi','controller'=>'Admin\\TransactionReportController','read'=>'index'];
        foreach(['privacy'=>'Kebijakan Privasi','terms'=>'Syarat dan Ketentuan','contact'=>'Kontak'] as $key=>$title) $modules[$key]=['title'=>$title,'controller'=>'Admin\\LegalController','read'=>$key,'form'=>true,'actions'=>['save'=>['method'=>'update'.ucfirst($key),'title'=>'Simpan']]];
        $modules['general']=['title'=>'Pengaturan Umum','controller'=>'Admin\\SettingController','read'=>'general','form'=>true,'settings'=>true,'actions'=>['save'=>['method'=>'saveGeneral','title'=>'Simpan pengaturan']]];
        $modules['popup']=['title'=>'Popup Situs','controller'=>'Admin\\PopupWidgetController','read'=>'edit','form'=>true,'actions'=>['save'=>['method'=>'update','title'=>'Simpan popup']]];
        $modules['profile']=['title'=>'Profil Admin','controller'=>'Admin\\ProfileController','read'=>'edit','form'=>true,'actions'=>['save'=>['method'=>'update','title'=>'Simpan profil']]];
        foreach(['hero'=>['Banner Utama','updateHero'],'features'=>['Panel Fitur','updateFeaturesPanel'],'texts'=>['Teks Beranda','updateSectionTexts']] as $key=>$row) $modules[$key]=['title'=>$row[0],'controller'=>'Admin\\HomeSettingController','read'=>'index','settings'=>true,'form'=>true,'actions'=>['save'=>['method'=>$row[1],'title'=>'Simpan']]];
        foreach(['banners'=>['Banner Beranda','Banner','HomeBanner'],'buttons'=>['Tombol Beranda','Button','HomeButton'],'locations'=>['Lokasi Beranda','Location','HomeLocation'],'testimonials'=>['Testimoni','Testimonial','Testimonial'],'bank-partners'=>['Partner Bank','BankPartner','BankPartner']] as $key=>$row) {
            $actions=['create'=>['method'=>'store'.$row[1],'title'=>'Tambah'],'delete'=>['method'=>'destroy'.$row[1],'title'=>'Hapus','record'=>true,'confirm'=>true]];
            if (!in_array($key,['banners','buttons'])) $actions['edit']=['method'=>'update'.$row[1],'title'=>'Edit','record'=>true];
            $modules[$key]=['title'=>$row[0],'controller'=>'Admin\\HomeSettingController','model'=>$row[2],'actions'=>$actions];
        }
        $modules['footer-logos']=['title'=>'Logo Footer','controller'=>'Admin\\SettingController','model'=>'FooterLogo','actions'=>['create'=>['method'=>'storeFooterLogo','title'=>'Tambah'],'delete'=>['method'=>'destroyFooterLogo','title'=>'Hapus','record'=>true,'confirm'=>true]]];
        $modules['offline-payment-methods']=['title'=>'Rekening Pembayaran','controller'=>'Admin\\OfflinePaymentMethodController','model'=>'OfflinePaymentMethod','actions'=>['create'=>['method'=>'store','title'=>'Tambah rekening'],'delete'=>['method'=>'destroy','title'=>'Hapus','record'=>true,'confirm'=>true]]];
        $modules['google-indexing']=['title'=>'Google Indexing','controller'=>'Admin\\GoogleIndexingController','actions'=>['upload'=>['method'=>'uploadJson','title'=>'Unggah kredensial JSON'],'submit'=>['method'=>'submitUrls','title'=>'Kirim URL','confirm'=>true]]];
        $modules['google-indexing']['actions']['connect']=['method'=>'oauthRedirect','title'=>'Hubungkan akun Google'];
        $modules['system']=['title'=>'Sistem','controller'=>'Admin\\SystemController','actions'=>['clear'=>['method'=>'clearCache','title'=>'Bersihkan cache','confirm'=>true]]];
        return ['dashboard'=>$modules['dashboard']] + $modules;
    }
}
