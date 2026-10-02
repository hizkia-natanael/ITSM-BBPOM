# BBPOM Semarang ITSM - Design System & UI/UX Guidelines

> **Standar Resmi UI/UX & Pencegahan AI Slop untuk Sistem Layanan & Pengaduan TI BBPOM di Semarang**  
> Seluruh pengembang dan AI Assistant (Antigravity, Cursor, Copilot, Claude, dsb.) **WAJIB** mematuhi panduan ini saat menambahkan, merombak, atau memelihara antarmuka aplikasi.

---

## 1. Filosofi Desain & Anti-AI Slop Manifesto

### Aturan Emas Anti-AI Slop
1. **Dilarang Keras Menggunakan Karakter Emoji (Zero Emojis Policy)**
   - **TIDAK BOLEH** ada karakter emoji seperti 🚀, 🔥, ⚠️, ✅, ❌, 📌, 💡, ⏱️ di seluruh kode Blade, HTML, alert, teks bantuan, maupun toast.
   - **Gunakan Secara Eksklusif:** [Remix Icon](https://remixicon.com/) versi 4.2+ dengan class `ri-*` (misal: `<i class="ri-checkbox-circle-fill"></i>`, `<i class="ri-history-line"></i>`).
2. **Wajib Meminta Saran `/impeccable` Sebelum Membangun Fitur Baru**
   - Setiap kali diminta merancang UI, komponen, atau alur pengguna baru, AI **TIDAK BOLEH** langsung menulis kode generik yang terkesan "template standar".
   - Berikan analisis `/impeccable` terlebih dahulu: berikan **2-3 opsi konsep desain terkurasi** lengkap dengan rasionalitas UX, kelebihan, dan rekomendasi terbaik. Tunggu persetujuan pengguna sebelum mengeksekusi kode.
3. **Desain Berkelas dengan Kedalaman Visual (*Depth & Craftsmanship*)**
   - Hindari kotak datar (*flat box*) dengan warna mencolok tanpa estetika.
   - Gunakan *frosted glass* (`backdrop-blur-md bg-slate-900/60`), border tipis bergradasi lembut (`border-slate-200/80` atau `border-emerald-500/20`), micro-animations berdurasi 200–300ms, serta bayangan tegas berdimensi (`shadow-xs` hingga `shadow-2xl`).

---

## 2. Palet Warna & Identitas Merek BBPOM

| Peran Warna | Tailwind Tokens | Penggunaan Utama |
| :--- | :--- | :--- |
| **Primary (Emerald BBPOM)** | `emerald-700`, `emerald-800`, `emerald-900` | Tombol utama, header aktif, badge status selesai, aksen brand. |
| **Primary Subtle** | `emerald-50`, `emerald-100`, `emerald-500/20` | Background chip, indikator aktif, ring fokus formulir. |
| **Neutral Deep (Slate)** | `slate-900`, `slate-800`, `slate-700` | Latar modal/toast glassmorphism, hero gradient, teks heading utama. |
| **Neutral Soft & Border** | `slate-50`, `slate-100`, `slate-200`, `slate-300` | Border kartu, input border, background tombol sekunder. |
| **Canvas Background** | `bg-gray-50` / `bg-slate-50` | Latar belakang seluruh halaman aplikasi. |
| **Status: Menunggu / Warning** | `amber-500`, `amber-600`, `amber-50`, `amber-100` | Tiket baru masuk, draf isian formulir, peringatan SLA. |
| **Status: Diproses / Info** | `blue-600`, `blue-50`, `sky-500` | Tiket sedang ditangani teknisi, sub-kategori terpilih. |
| **Status: Selesai / Resolved** | `emerald-600`, `emerald-700`, `emerald-50` | Tiket tertutup, formulir sukses dikirim, pemulihan draf berhasil. |
| **Status: Ditolak / Danger** | `rose-600`, `rose-700`, `rose-50` | Validasi error, tiket dibatalkan, konfirmasi penghapusan akun. |

---

## 3. Tipografi & Spacing

* **Font Utama:** `Inter`, didukung oleh font system bawaan (`font-sans`).
* **Hirarki Judul:**
  * H1 / H2 Halaman: `text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900`
  * H3 / Card Title: `text-lg sm:text-xl font-bold text-slate-900`
  * Label Input: `text-xs font-bold uppercase tracking-wider text-slate-800`
  * Body Text: `text-xs sm:text-sm text-slate-600 leading-relaxed`
  * Metadata / Helper Text: `text-[11px] font-medium text-slate-400`
* **Border Radius:**
  * Kartu & Modal Panel: `rounded-2xl` atau `rounded-3xl`
  * Tombol & Input Field: `rounded-xl` atau `rounded-2xl`
  * Badge & Pill: `rounded-full`

---

## 4. Arsitektur Komponen (Atomic Design Standard)

Semua komponen antarmuka yang dipakai ulang **WAJIB** berada di direktori `resources/views/components/` dan dipanggil menggunakan sintaks `<x-nama-komponen />`.

```
resources/views/components/
├── application-logo.blade.php           # Atom: Logo resmi
├── primary-button.blade.php             # Atom: Tombol primer emerald
├── secondary-button.blade.php           # Atom: Tombol sekunder slate
├── text-input.blade.php                 # Atom: Input form standar
├── input-label.blade.php                # Atom: Label teks input
├── ticket-status-badge.blade.php        # Molecule: Badge status tiket warna-warni & identitas admin penangan
├── ticket-category-badge.blade.php      # Molecule: Badge kategori layanan berformat rounded-full & ikon Remix Icon kontekstual
├── ticket-handler-card.blade.php         # Molecule: Kartu profil & status teknisi/admin penanggung jawab
├── sla-info-card.blade.php              # Molecule: Kartu aturan SLA layanan
├── draft-recovery-alert.blade.php       # Organism: Modal glassmorphism draf tersimpan
├── leave-form-confirmation-modal.blade.php # Organism: Modal konfirmasi keluar halaman
├── ticket-status-stepper.blade.php      # Organism: Stepper tracking status tiket
└── ticket-description.blade.php         # Organism: Card detail percakapan tiket
```

> **Larangan:** Jangan pernah menduplikasi blok kode modal, badge, atau stepper secara inline di halaman Blade individual. Buat atau perbarui komponen atomic-nya.

---

## 5. Standar Penulisan JavaScript & Alpine.js

1. **Dilarang Menulis Operator HTML di Atribut Inline**:
   - **JANGAN PERNAH** menulis operator perbandingan seperti `length > 0`, `< 5`, atau operator logika majemuk `&&` di dalam atribut inline `x-data="{ ... }"`.
   - **Penyebab**: Browser HTML parser akan salah membaca karakter `>` sebagai penutup tag HTML `</div>`, menyebabkan kode JavaScript bocor (*leak*) dan tercetak sebagai teks mentah di layar pengguna.
2. **Ekstraksi ke Fungsi Script Standalone**:
   - Selalu buat fungsi terdedikasi di tag `<script>` (contoh: `function ticketGatewayData() { return { ... }; }`) dan inisialisasi dengan `<div x-data="ticketGatewayData()">`.
3. **Penyimpanan Draf & Autosave**:
   - Autosave formulir harus berjalan senyap melalui event input ter-debounce: `@input.debounce.500ms="saveDraft()"`.
   - Gunakan `localStorage` dengan prefix jelas (misal: `bbpom_itsm_ticket_draft`).

---

## 6. Protokol Dialog & Modal Pengguna

1. **Ketika Halaman Dimuat Ulang / Ter-refresh**:
   - Jangan gunakan dialog bawaan browser yang kaku (*"Leave site?"*).
   - Biarkan data tersimpan otomatis ke draf lokal secara senyap.
   - Sambut pengguna dengan modal **Centered Glassmorphism Modal** (`draft-recovery-alert.blade.php`) yang menyajikan preview nama layanan, jumlah isian yang diamankan, dan tombol *"Lanjutkan Pengisian"*.
2. **Ketika Mengklik Link Navigasi Internal**:
   - Intersep navigasi saat formulir terisi (`isDirty()`) dengan modal kustom `leave-form-confirmation-modal.blade.php`.
   - Jelaskan bahwa data pengguna aman dan beri pilihan *"Lanjutkan Mengisi"* atau *"Tinggalkan Halaman"*.

---

## 7. Quality Assurance & Verification Checklist

Sebelum melakukan commit atau menyatakan pekerjaan selesai:
1. Jalankan pengujian frontend:
   ```bash
   npm run build
   ```
   Pastikan tidak ada sintaks error di file CSS/JS.
2. Jalankan test suite backend Laravel:
   ```bash
   php artisan test
   ```
   Pastikan seluruh test suite (minimal 37 tests) berstatus **PASS** (100%).
3. Periksa tampilan visual:
   - Pastikan tidak ada karakter emoji.
   - Pastikan ikon menggunakan Remix Icon (`ri-*`).
   - Pastikan tidak ada teks JavaScript mentah bocor di tampilan HTML.
