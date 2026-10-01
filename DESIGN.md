---
name: Helpdesk BBPOM Semarang
description: Portal Katalog Layanan & Helpdesk Pengelolaan TIK Balai Besar POM di Semarang
colors:
  primary: "#047857"
  primary-dark: "#065f46"
  primary-light: "#ecfdf5"
  neutral-dark: "#0f172a"
  neutral-body: "#334155"
  neutral-muted: "#64748b"
  neutral-border: "#cbd5e1"
  neutral-bg: "#f8fafc"
  status-pending: "#f59e0b"
  status-diproses: "#2563eb"
  status-selesai: "#059669"
  status-ditolak: "#e11d48"
typography:
  display:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 700
    lineHeight: 1.25
  body:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
  code:
    fontFamily: "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace"
    fontSize: "0.8125rem"
  caption:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.6875rem"
  micro:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.625rem"
rounded:
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "24px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
---

# Design System

<!-- impeccable:design-schema 1 -->

## Overview
Design system untuk Helpdesk BBPOM di Semarang beroperasi dalam mode **Operate**: antarmuka terstruktur, tajam, profesional, dengan hirarki visual yang jelas untuk memastikan pengajuan dan tindak lanjut permohonan TIK berjalan cepat dan tanpa ambigu.

## Colors
- **Primary (Hijau Khas BBPOM)**:
  - `primary` (`#047857` / `emerald-700`): Tombol aksi primer, ikon aktif, indikator terpilih.
  - `primary-dark` (`#065f46` / `emerald-800`): State hover tombol primer, teks penekanan.
  - `primary-light` (`#ecfdf5` / `emerald-50`): Latar badge dan kartu layanan aktif.
- **Neutrals**:
  - `neutral-dark` (`#0f172a` / `slate-900`): Judul utama, teks label dengan kontras maksimal.
  - `neutral-body` (`#334155` / `slate-700`): Konten bacaan dan input teks.
  - `neutral-muted` (`#64748b` / `slate-500`): Teks penjelas (helper text) dan metadata.
  - `neutral-border` (`#cbd5e1` / `slate-300`): Garis batas form dan card.
  - `neutral-bg` (`#f8fafc` / `slate-50`): Latar belakang field non-aktif dan card pendukung.
- **Status (ITSM Lifecycle)**:
  - `status-pending`: Amber (`#f59e0b`)
  - `status-diproses`: Blue (`#2563eb`)
  - `status-selesai`: Emerald (`#059669`)
  - `status-ditolak`: Rose (`#e11d48`)

## Typography
- **Font Utama**: Inter dipadukan dengan system-ui sans-serif.
- **Font Monospace**: Digunakan secara ketat untuk Nomor Tiket (`TIK-BBPOM-xxx`), NIP (18 digit), NIK (16 digit), dan kode ekstensi telepon.
- **Skala Ukuran**:
  - Judul Halaman: `text-xl font-bold text-slate-900`
  - Sub-judul & Section: `text-base font-bold text-slate-900`
  - Nama Kartu / Modul: `text-sm font-bold text-slate-900`
  - Label Form: `text-xs font-semibold text-slate-700`
  - Helper & Deskripsi Kecil: `text-[11px] text-slate-500`

## Layout
- Layout grid 12-kolom responsif:
  - Katalog: 1 kolom di mobile, 2 kolom di tablet, 3-4 kolom di desktop.
  - Formulir Pengaduan: 2 kolom (form utama) + 1 kolom (panduan/SLA/kontak darurat).
- Spacing konsisten: Kontainer utama `space-y-6`, padding card `p-6 sm:p-8`, gap form `gap-4`.

## Elevation & Depth
- Flat & Tonal Layering: Menghindari bayangan berat yang artifisial. Menggunakan border halus `border-slate-200/90` dengan bayangan tipis `shadow-sm`.
- Elemen interaktif mendapat elevasi taktil saat hover (`hover:shadow-md hover:-translate-y-0.5`).

## Shapes
- Radius Kontainer: `rounded-2xl` (16px) untuk kartu informasi dan form; `rounded-3xl` (24px) untuk hero search banner.
- Radius Kontrol: `rounded-xl` (12px) untuk input text, select box, dan tombol.
- Radius Badge: `rounded-full` atau `rounded-lg` (8px).

## Components
- **Button Primary**: `bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl px-5 py-2.5 shadow-md shadow-emerald-700/20 active:scale-[0.99] transition`.
- **Button Secondary**: `border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl px-4 py-2.5 transition`.
- **Input Text / Select**: `rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm`.
- **Field Read-Only**: `rounded-xl bg-slate-100/80 border-slate-200 text-slate-700 font-semibold text-xs sm:text-sm cursor-not-allowed`.
- **Status Indicator**: Badge pill rounded dengan teks tebal kontras tinggi.

## Do's and Don'ts
- **DO**: Selalu gunakan teks putih murni (`text-white`) saat berada di atas latar belakang berwarna tegas (`bg-emerald-600`, `bg-blue-600`, `bg-amber-500`).
- **DO**: Tampilkan data identitas (NIP, NIK, tanggal) dalam format monospace atau format tanggal bahasa Indonesia baku (`d F Y`).
- **DO**: Sediakan panduan SLA dan jalur komunikasi darurat di samping setiap formulir.
- **DON'T**: Jangan pernah menaruh teks abu-abu pudar di atas background warna (`gray-on-color`).
- **DON'T**: Jangan gunakan layout form yang menumpuk tanpa pengelompokan yang jelas.
