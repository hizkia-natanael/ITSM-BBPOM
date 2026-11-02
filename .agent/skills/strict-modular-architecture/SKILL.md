---
name: strict-modular-architecture
description: Enforce strict modular architecture and a 100-120 line limit per file. Mandates automatic refactoring into sub-components, services, custom hooks, and utility modules whenever any file approaches or exceeds 120 lines.
---

# Antigravity Skill: Strict Modular Architecture & Code Limits

## Tujuan (Objective)
Skill ini memastikan bahwa setiap kode yang dihasilkan, dimodifikasi, atau di-refactor selalu mematuhi batas maksimal baris kode dan diorganisasikan dalam struktur folder yang sangat modular berdasarkan fungsionalitasnya.

---

## Aturan Inti (Core Rules)

### 1. Batas Baris Kode (Line Limit Constraint)
- **Maksimal:** 100 - 120 baris per file.
- **Tindakan Wajib (Mandatory Action):** Jika sebuah file/komponen berpotensi melebihi 120 baris, **JANGAN** dilanjutkan dalam satu file. Anda **WAJIB** melakukan refactoring secara otomatis dengan memecahnya menjadi sub-komponen, utilitas, action classes, atau *custom hooks*.
- **Fokus:** Terapkan *Single Responsibility Principle (SRP)*. Satu file hanya boleh melakukan satu tugas atau merender satu blok UI/logika spesifik.

### 2. Struktur Folder Modular (Folder Separation)
Setiap komponen, fungsi, atau aset harus dipisahkan ke dalam folder yang relevan. Jangan menumpuk file dalam direktori root.

#### A. Arsitektur Frontend (React / Vue / TypeScript / Next / Vite)
```text
src/
  components/       # Komponen UI utama (contoh: Button, Card, Header)
      ui/          # Komponen dasar/primitif (stateless)
      layout/      # Komponen pembungkus (wrapper/grid/container)
  features/         # Komponen spesifik untuk fitur tertentu (contoh: Auth, Dashboard)
  hooks/            # Custom logic/hooks untuk memisahkan state dari UI
  utils/            # Fungsi helper murni (pure functions), formatter, kalkulasi
  services/         # Integrasi API, fetch data, eksekusi eksternal
  types/            # Definisi tipe data (TypeScript interfaces/types)
  constants/        # Variabel statis, konfigurasi, nilai konstan
```

#### B. Arsitektur Fullstack Laravel / Blade / Alpine.js
```text
app/
  Actions/          # Single-action classes untuk proses bisnis spesifik (< 100 baris)
  Services/         # Domain services terisolasi (Export, Notification, Integration)
  Http/
    Controllers/    # Thin controllers (< 100 baris)
    Requests/       # FormRequest validasi terpisah
resources/
  views/
    components/     # Blade atomic components (sub-folder per domain/fitur)
        ui/        # Tombol, badge, alert, modal primitif
        layout/    # Card wrapper, grid shell, page header
        [feature]/ # Sub-komponen spesifik per fitur (auth, tickets, guide-book)
  js/
    alpine/         # Alpine.js state modules & controller functions terpisah
    utils/          # Helper JavaScript murni
```

---

## Prosedur Refactoring Otomatis

1. **Identifikasi File Melebihi Batas (> 120 baris)**:
   - Evaluasi apakah terdapat multiple responsibilities (misal UI + form validation + API logic + state management dalam satu file).
2. **Pecah Berdasarkan Tanggung Jawab (Extract Sub-units)**:
   - Ekstrak elemen UI modular ke dalam folder `components/` atau sub-komponen atomik.
   - Ekstrak state/logika ke dalam custom hook, file JS/Alpine terpisah, atau Service/Action class.
   - Pindahkan konstanta atau konfigurasi ke file constants tersendiri.
3. **Verifikasi Keutuhan**:
   - Pastikan interkoneksi prop/event antar komponen tetap bekerja 100%.
   - Jalankan `npm run build` dan automated tests untuk memastikan tidak ada regresi fungsional.
