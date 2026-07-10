<?php

return [

    // Base
    'admin.dashboard.view' => ['label' => 'Dashboard Admin', 'matches' => ['admin.dashboard']],
    'admin.profile.manage' => ['label' => 'Profil Admin', 'matches' => ['admin.profile.*']],

    // Core transaksi
    'admin.orders.manage'   => ['label' => 'Orders', 'matches' => ['admin.orders.*']],
    'admin.payments.manage' => ['label' => 'Pembayaran', 'matches' => ['admin.payments.*']],

    // Paket Wisata
    'admin.tour-packages.manage' => ['label' => 'CRUD Paket Tour', 'matches' => ['admin.tour-packages.*']],
    'admin.categories.manage'    => ['label' => 'CRUD Kategori Tour', 'matches' => ['admin.categories.*']],

    // Rental Mobil
    'admin.rent-car-packages.manage'   => ['label' => 'CRUD Paket Rental Mobil', 'matches' => ['admin.rent-car-packages.*']],
    'admin.rent-car-categories.manage' => ['label' => 'CRUD Kategori Rental Mobil', 'matches' => ['admin.rent-car-categories.*']],

    // Sewa Kapal
    'admin.ship-packages.manage'   => ['label' => 'CRUD Paket Sewa Kapal', 'matches' => ['admin.ship-packages.*']],
    'admin.ship-categories.manage' => ['label' => 'CRUD Kategori Kapal', 'matches' => ['admin.ship-categories.*']],

    // Umrah
    'admin.umrah-packages.manage'   => ['label' => 'CRUD Paket Umrah', 'matches' => ['admin.umrah-packages.*']],
    'admin.umrah-categories.manage' => ['label' => 'CRUD Kategori Umrah', 'matches' => ['admin.umrah-categories.*']],

    // MICE
    'admin.mice-packages.manage'   => ['label' => 'CRUD Paket MICE', 'matches' => ['admin.mice-packages.*']],
    'admin.mice-categories.manage' => ['label' => 'CRUD Kategori MICE', 'matches' => ['admin.mice-categories.*']],

    // Konten & tampilan
    'admin.client-logos.manage'            => ['label' => 'Client Logos', 'matches' => ['admin.client-logos.*']],
    'admin.promos.manage'                  => ['label' => 'Promo', 'matches' => ['admin.promos.*']],
    'admin.articles.manage'                => ['label' => 'Articles', 'matches' => ['admin.articles.*']],
    'admin.destination-inspirations.manage' => ['label' => 'Destination Inspirations', 'matches' => ['admin.destination-inspirations.*']],
    'admin.reviews.manage'                 => ['label' => 'Komentar Paket', 'matches' => ['admin.reviews.*']],
    'admin.home-sections.manage'           => ['label' => 'Home Sections (Promo Tours)', 'matches' => ['admin.home-sections.*']],

    // System pages
    'admin.seo.manage'        => ['label' => 'SEO', 'matches' => ['admin.seo.*']],
    'admin.legal-pages.manage'=> ['label' => 'Legal Pages', 'matches' => ['admin.legal-pages.*']],
    'admin.settings.manage'   => ['label' => 'Settings', 'matches' => ['admin.settings.*']],
    'admin.system.manage'     => ['label' => 'System (Clear Cache)', 'matches' => ['admin.system.*']],

    // Users
    'admin.users.manage' => ['label' => 'Manajemen User', 'matches' => ['admin.users.*']],
    'admin.notifications.manage' => ['label' => 'Kirim Notifikasi', 'matches' => ['admin.notifications.*']],

    // Affiliate (prefix admin.affiliate.* dan admin.users.affiliate.*)
    'admin.affiliate.requests.manage'     => ['label' => 'Affiliate Requests', 'matches' => ['admin.affiliate.requests.*']],
    'admin.affiliate.orders.manage'       => ['label' => 'Affiliate Orders', 'matches' => ['admin.affiliate.orders.*']],
    'admin.affiliate.withdrawals.manage'  => ['label' => 'Affiliate Withdrawals', 'matches' => ['admin.affiliate.withdrawals.*']],
    'admin.affiliate.users.manage'        => ['label' => 'Affiliate Users', 'matches' => ['admin.users.affiliate.*']],

    // Partner management (admin.partners.* dan admin.partner_withdrawals.*)
    'admin.partners.applications.manage'  => ['label' => 'Partner Applications', 'matches' => ['admin.partners.applications.*']],
    'admin.partners.users.manage'         => ['label' => 'Partner Users', 'matches' => ['admin.partners.users.*']],
    'admin.partners.products.manage'      => ['label' => 'Produk Partner', 'matches' => ['admin.partners.products.*']],
    'admin.partner_withdrawals.manage'    => ['label' => 'Partner Withdrawals', 'matches' => ['admin.partner_withdrawals.*']],
];
