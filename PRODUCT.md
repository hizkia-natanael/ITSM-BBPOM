# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users
Pegawai ASN, staf administrasi, teknisi laboratorium, serta tim teknis Pengelola TIK Balai Besar Pengawas Obat dan Makanan (BBPOM) di Semarang.

## Product Purpose
Portal Helpdesk & Service Catalog TIK resmi internal BBPOM di Semarang untuk pengajuan permohonan layanan, pelaporan insiden/kendala teknis (SIPT, Srikandi, Email Corporate, Sertifikat BSrE / TTE, Zoom, Jaringan & Internet, Hardware/PC, dll.), serta pemantauan status tindak lanjut penanganan insiden dan permohonan secara real-time.

## Positioning
Portal layanan ITSM internal satu pintu (One-Stop Service Gateway) yang kontekstual dan adaptif: alih-alih formulir generik yang membingungkan pegawai, sistem menyediakan gerbang katalog 12 layanan spesifik dengan skema formulir dan database yang disesuaikan secara terstruktur (seperti Sertifikat Elektronik BSrE dengan auto-fill identitas dan sub-kategori khusus).

## Operating Context
- Digunakan oleh pegawai kantor dan laboratorium BBPOM Semarang pada perangkat desktop PC kantor maupun mobile/tablet.
- Alur kerja cepat dan minim friksi: pengisian formulir dengan pre-filled data akun login, NIP otomatis tersimpan ke profil, dan pelacakan tiket transparan.
- Mode operasional: **Operate** (tugas terselesaikan cepat, scannable, kontras tajam, tidak ada elemen dekoratif palsu).

## Capabilities and Constraints
- Layanan Aplikasi: SIPT, Srikandi, Email Corporate, Sertifikat BSrE, Zoom, Formulir/Kegiatan, Jaringan & Internet, Lisensi Office & Windows, Instalasi Printer, Perbaikan PC & Laptop, Backup Data, serta Layanan TI Lainnya.
- Penanganan khusus BSrE: Reset Password BSRE, Permintaan OTP, dan Pengaduan Lainnya dengan skema basis data terdedikasi (`ticket_bsre_details`).
- Backend: Laravel 12, Livewire/Alpine.js, Tailwind CSS, Filament v4 Admin Panel, MariaDB/MySQL.

## Brand Commitments
- Identitas BBPOM di Semarang: Palet resmi Hijau Zamrud BBPOM (`#047857`, emerald/slate), lambang resmi BPOM RI, nuansa profesional, birokrasi modern, terpercaya, dan aman.
- Bahasa: Bahasa Indonesia baku, jelas, informatif, santun kedinasan.

## Product Principles
1. **Kejelasan di Atas Ornamen**: Utamakan kejelasan informasi, keterbacaan data teknis (NIP, nomor tiket, status), dan minimalkan ornamen tanpa fungsi.
2. **Konteks Nyata & Akurat**: Tidak menggunakan data placeholder fiktif; setiap label dan instruksi harus merefleksikan alur kerja kedinasan riil.
3. **Minim Friksi & Cerdas**: Gunakan otomatisasi pre-fill (nama, tanggal hari ini, NIP profil) agar pegawai tidak mengulang pengisian data berulang kali.
4. **Scannability & Realistis**: Status tiket, ID tiket, dan aksi utama harus dapat dipindai secara visual dalam 2 detik.
