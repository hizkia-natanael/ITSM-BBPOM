<?php

return [
    /*
    |--------------------------------------------------------------------------
    | App Gateway: Katalog Layanan TIK BBPOM di Semarang
    |--------------------------------------------------------------------------
    | Konfigurasi daftar 12 menu layanan utama yang tersedia pada portal helpdesk.
    | Rekan pengembang dapat mengisi array 'subcategories' untuk masing-masing
    | layanan di bawah ini sesuai kebutuhan pembagian tugas.
    | Ikon menggunakan icon library resmi (Remix Icon).
    */

    'groups' => [
        'all' => 'Semua Layanan',
        'sistem' => 'Sistem & Aplikasi',
        'akun' => 'Akun & Sertifikasi',
        'kegiatan' => 'Kegiatan & Media',
        'infrastruktur' => 'Infrastruktur & OS',
    ],

    'services' => [
        'sipt' => [
            'id' => 'sipt',
            'name' => 'SIPT',
            'long_name' => 'Sistem Informasi Pelaporan Terpadu',
            'group' => 'sistem',
            'badge' => 'Aplikasi Pusat BPOM',
            'icon' => 'ri-file-chart-line',
            'description' => 'Kendala akses pelaporan, input pengujian, validasi data, dan error operasional aplikasi SIPT.',
            'keywords' => ['sipt', 'laporan', 'pelaporan', 'pengujian', 'data', 'bpom', 'sistem'],
            'color' => 'indigo',
            'default_category_id' => 5,
            'subcategories' => [],
        ],

        'srikandi' => [
            'id' => 'srikandi',
            'name' => 'Srikandi',
            'long_name' => 'Sistem Kearsipan Dinamis Terintegrasi',
            'group' => 'sistem',
            'badge' => 'Kearsipan & Persuratan',
            'icon' => 'ri-archive-2-line',
            'description' => 'Bantuan registrasi naskah dinas, penandatanganan elektronik (TTE), login, dan mutasi akun Srikandi.',
            'keywords' => ['srikandi', 'surat', 'arsip', 'tte', 'naskah dinas', 'kearsipan', 'persuratan'],
            'color' => 'blue',
            'default_category_id' => 5,
            'subcategories' => [],
        ],

        'email_corporate' => [
            'id' => 'email_corporate',
            'name' => 'Email Corporate',
            'long_name' => 'Email Resmi Pegawai BBPOM (@bbpom.go.id)',
            'group' => 'akun',
            'badge' => 'Komunikasi Resmi',
            'icon' => 'ri-mail-line',
            'description' => 'Pembuatan akun baru, reset password, kuota penyimpanan penuh, serta kendala kirim/terima webmail.',
            'keywords' => ['email', 'mail', 'corporate', 'webmail', 'password', 'bbpom.go.id', 'kuota', 'inbox'],
            'color' => 'sky',
            'default_category_id' => 7,
            'subcategories' => [],
        ],

        'sertifikat_bse' => [
            'id' => 'sertifikat_bse',
            'name' => 'Sertifikat BSE',
            'long_name' => 'Balai Sertifikasi Elektronik / TTE',
            'group' => 'akun',
            'badge' => 'Keamanan & TTE',
            'icon' => 'ri-shield-keyhole-line',
            'description' => 'Penerbitan baru, perpanjangan masa berlaku sertifikat elektronik, lupa passphrase, dan verifikasi dokumen.',
            'keywords' => ['bse', 'sertifikat', 'elektronik', 'tte', 'tanda tangan', 'passphrase', 'pin', 'bsre'],
            'color' => 'amber',
            'default_category_id' => 7,
            'subcategories' => [],
        ],

        'zoom' => [
            'id' => 'zoom',
            'name' => 'Zoom Meeting / Webinar',
            'long_name' => 'Layanan Ruang Virtual Meeting & Webinar',
            'group' => 'kegiatan',
            'badge' => 'Virtual Meeting',
            'icon' => 'ri-video-chat-line',
            'description' => 'Peminjaman lisensi akun Zoom, pendampingan teknis rapat, pengaturan breakout room, dan live streaming.',
            'keywords' => ['zoom', 'meeting', 'webinar', 'virtual', 'rapat', 'breakout', 'streaming'],
            'color' => 'cyan',
            'default_category_id' => 5,
            'subcategories' => [],
        ],

        'formulir_kegiatan' => [
            'id' => 'formulir_kegiatan',
            'name' => 'Formulir & Kegiatan',
            'long_name' => 'Daftar Hadir, QR Code, Survey, Pre/Post-Test & Link Tree',
            'group' => 'kegiatan',
            'badge' => 'Event & Survey Tools',
            'icon' => 'ri-survey-line',
            'description' => 'Pembuatan presensi online, generate QR Code resmi, form survei kepuasan, pretest/posttest, dan tautan kegiatan.',
            'keywords' => ['daftar hadir', 'presensi', 'qrcode', 'qr code', 'survey', 'kuesioner', 'pretest', 'posttest', 'link tree', 'linktree', 'formulir'],
            'color' => 'emerald',
            'default_category_id' => 5,
            'subcategories' => [],
        ],

        'internet' => [
            'id' => 'internet',
            'name' => 'Internet & Jaringan',
            'long_name' => 'Koneksi WiFi Kantor, LAN & Akses VPN',
            'group' => 'infrastruktur',
            'badge' => 'Jaringan Kantor',
            'icon' => 'ri-wifi-line',
            'description' => 'Kendala koneksi internet lambat/putus, kabel LAN ruangan, akun WiFi kantor, dan konfigurasi akses VPN.',
            'keywords' => ['internet', 'wifi', 'wi-fi', 'lan', 'kabel', 'jaringan', 'koneksi', 'vpn', 'hotspot', 'rto', 'router'],
            'color' => 'teal',
            'default_category_id' => 3,
            'subcategories' => [],
        ],

        'subsite' => [
            'id' => 'subsite',
            'name' => 'Subsite BBPOM',
            'long_name' => 'Portal Subsite Resmi BBPOM di Semarang',
            'group' => 'kegiatan',
            'badge' => 'Website Portal',
            'icon' => 'ri-global-line',
            'description' => 'Pembaruan berita/artikel, penambahan menu informasi publik, dan penyesuaian tata letak subsite resmi.',
            'keywords' => ['subsite', 'website', 'web', 'artikel', 'berita', 'menu', 'portal', 'semarang', 'laman'],
            'color' => 'violet',
            'default_category_id' => 5,
            'subcategories' => [],
        ],

        'infografis' => [
            'id' => 'infografis',
            'name' => 'Infografis & Media',
            'long_name' => 'Desain Grafis Publikasi & Spanduk Digital',
            'group' => 'kegiatan',
            'badge' => 'Desain Publikasi',
            'icon' => 'ri-image-line',
            'description' => 'Permohonan desain materi sosialisasi, spanduk acara dinas, infografis medsos, dan materi presentasi resmi.',
            'keywords' => ['infografis', 'desain', 'grafis', 'banner', 'spanduk', 'poster', 'flyer', 'publikasi', 'media', 'canva'],
            'color' => 'rose',
            'default_category_id' => 5,
            'subcategories' => [],
        ],

        'install_windows' => [
            'id' => 'install_windows',
            'name' => 'Install Ulang Windows',
            'long_name' => 'Operating System PC & Laptop Dinas',
            'group' => 'infrastruktur',
            'badge' => 'Sistem Operasi',
            'icon' => 'ri-computer-line',
            'description' => 'Re-instalasi OS Windows, pembersihan virus/malware, PC lambat, upgrade versi, dan pemulihan data sistem.',
            'keywords' => ['install', 'windows', 'os', 'format', 'laptop', 'pc', 'komputer', 'virus', 'lambat', 'reinstall', 'win 11'],
            'color' => 'slate',
            'default_category_id' => 4,
            'subcategories' => [],
        ],

        'lisensi_office' => [
            'id' => 'lisensi_office',
            'name' => 'Lisensi Microsoft Office',
            'long_name' => 'Aktivasi Microsoft 365, Word, Excel & PPT',
            'group' => 'infrastruktur',
            'badge' => 'Lisensi Resmi',
            'icon' => 'ri-key-line',
            'description' => 'Aktivasi lisensi resmi, penanganan error unlicensed/expired, dan instalasi software perkantoran Microsoft.',
            'keywords' => ['lisensi', 'license', 'microsoft', 'office', 'word', 'excel', 'powerpoint', 'ms office', 'unlicensed', 'aktivasi'],
            'color' => 'orange',
            'default_category_id' => 5,
            'subcategories' => [],
        ],

        'lainnya' => [
            'id' => 'lainnya',
            'name' => 'Lainnya',
            'long_name' => 'Kendala Hardware, Printer & Bantuan Khusus TI',
            'group' => 'infrastruktur',
            'badge' => 'Dukungan Umum',
            'icon' => 'ri-tools-line',
            'description' => 'Kerusakan printer, scanner, proyektor ruang rapat, atau konsultasi kebutuhan teknologi informasi lainnya.',
            'keywords' => ['lainnya', 'printer', 'scanner', 'proyektor', 'audio', 'hardware', 'bantuan', 'kendala', 'konsultasi', 'mati'],
            'color' => 'gray',
            'default_category_id' => 6,
            'subcategories' => [],
        ],
    ],
];
