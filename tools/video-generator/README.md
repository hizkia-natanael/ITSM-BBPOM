# ELITE BBPOM - Automated Video Tutorial Generator

Modul otomasi pembuatan video tutorial sistem **ELITE (E-Layanan IT Terintegrasi) Balai Besar POM di Semarang**. Menggabungkan tiga pilar otomasi:
1. **Playwright**: Merekam alur nyata sistem helpdesk pada browser beresolusi 1080p tanpa rekaman manual.
2. **Edge-TTS**: Menghasilkan suara narator resmi Bahasa Indonesia (`id-ID-ArdiNeural`) secara instan dari naskah JSON.
3. **Remotion**: Menyusun video, transisi animasi, kartu langkah (step cards), branding korporat BBPOM, dan subtitle secara terprogram (*Video-as-Code*).

---

## Prasyarat
1. Server lokal ELITE aktif di background (`http://127.0.0.1:8000`).
2. Node.js (v20+).

---

## Perintah Eksekusi Otomatis

### 1. Eksekusi Seluruh Pipeline (1 Perintah)
Menjalankan pemeriksaan server, generate audio narator, perekaman layar Playwright, sinkronisasi aset, dan render video MP4:
```bash
cd tools/video-generator
npm run build-all
```

Hasil render akhir tersimpan di:
`tools/video-generator/out/tutorial-pengajuan-tiket.mp4`

---

### 2. Eksekusi Per Modul Terpisah

* **Generate Suara Narator Saja:**
  ```bash
  npm run tts
  ```
* **Jalankan Perekaman Browser Playwright Saja:**
  ```bash
  npm run capture
  ```
* **Buka Studio Interaktif Remotion (Pratinjau Langsung di Browser):**
  ```bash
  npm run preview
  ```
* **Render Video Akhir ke MP4:**
  ```bash
  npm run render
  ```

---

## Struktur Direktori

* `content/scenario-a.json`: File konfigurasi teks naskah narasi dan tahapan langkah.
* `scripts/generate-tts.js`: Skrip generator suara Edge-TTS Bahasa Indonesia.
* `scripts/capture.js`: Skrip Playwright untuk menjalankan skenario interaksi browser.
* `scripts/build-tutorial.js`: Orkestrator pipeline menyeluruh.
* `src/TutorialComposition.tsx`: Komposisi visual Remotion (overlay branding, lower-third cards, progress bar).
* `out/`: Folder output untuk audio, rekaman mentah, dan file video final `.mp4`.
