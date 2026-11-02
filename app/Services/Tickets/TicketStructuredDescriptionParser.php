<?php

namespace App\Services\Tickets;

class TicketStructuredDescriptionParser
{
    public static function parse(?string $desc): array
    {
        $desc = $desc ?: '';
        $isStructured = str_contains($desc, '**Layanan:**') || str_contains($desc, '### Rincian Kebutuhan');

        if (!$isStructured) {
            return ['is_structured' => false, 'meta' => [], 'fields' => [], 'long_fields' => []];
        }

        $meta = [];
        $fields = [];
        $longFields = [];
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $desc));
        $inRincian = false;
        $currentLongKey = null;
        $currentLongVal = [];
        $lastFieldKey = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '---') {
                continue;
            }
            if (str_starts_with($trimmed, '### Rincian Kebutuhan')) {
                $inRincian = true;
                continue;
            }
            if (!$inRincian) {
                if ($trimmed !== '' && preg_match('/^\*\*(.+?):\*\*\s*(.*)$/', $trimmed, $m)) {
                    $meta[trim($m[1])] = trim($m[2]);
                }
                continue;
            }

            if (preg_match('/^(\*|-)?\s*\*\*(.+?):\*\*\s*(.*)$/', $trimmed, $m)) {
                if ($currentLongKey !== null) {
                    $text = trim(implode("\n", $currentLongVal));
                    if ($text !== '') {
                        $longFields[$currentLongKey] = $text;
                    }
                    $currentLongKey = null;
                    $currentLongVal = [];
                }

                $key = trim($m[2]);
                $val = trim($m[3]);
                $isLong = $val === '' || strlen($val) > 80 || (bool) preg_match('/(deskripsi|keterangan|catatan|materi|konten|berita|pesan|keluhan|alasan|perbaikan|permintaan|detail|narasi)/i', $key);

                if ($isLong) {
                    $currentLongKey = $key;
                    $currentLongVal = $val !== '' ? [$val] : [];
                    $lastFieldKey = null;
                    unset($fields[$key]);
                } elseif (!isset($fields[$key])) {
                    $fields[$key] = $val;
                    $lastFieldKey = $key;
                }
            } elseif ($currentLongKey !== null) {
                $currentLongVal[] = $line;
            } elseif ($lastFieldKey !== null && $trimmed !== '') {
                $currentLongKey = $lastFieldKey;
                $currentLongVal = [$fields[$lastFieldKey], $line];
                unset($fields[$lastFieldKey]);
                $lastFieldKey = null;
            }
        }

        if ($currentLongKey !== null) {
            $text = trim(implode("\n", $currentLongVal));
            if ($text !== '') {
                $longFields[$currentLongKey] = $text;
            }
        }

        return ['is_structured' => true, 'meta' => $meta, 'fields' => $fields, 'long_fields' => $longFields];
    }

    public static function resolveFieldIcon(string $key): string
    {
        $k = strtolower($key);
        $map = [
            'ri-user-3-line' => ['user', 'nama', 'pemohon'],
            'ri-lock-password-line' => ['pass', 'sandi'],
            'ri-test-tube-line' => ['sampel', 'kode'],
            'ri-mail-line' => ['email', 'mail'],
            'ri-global-line' => ['domain', 'url', 'link', 'subsite'],
            'ri-router-line' => ['ip', 'mac', 'port', 'lan', 'wifi'],
            'ri-phone-line' => ['telepon', 'wa', 'kontak', 'hp'],
            'ri-map-pin-2-line' => ['lokasi', 'ruang', 'gedung'],
            'ri-building-4-line' => ['unit', 'seksi', 'bidang'],
            'ri-calendar-event-line' => ['acara', 'kegiatan', 'rapat'],
            'ri-time-line' => ['jam', 'waktu', 'tanggal', 'deadline', 'tenggat'],
            'ri-price-tag-3-line' => ['tipe', 'kategori', 'jenis'],
            'ri-aspect-ratio-line' => ['orientasi', 'ukuran'],
            'ri-computer-line' => ['perangkat', 'laptop', 'komputer', 'pc', 'merk'],
            'ri-barcode-line' => ['bmn'],
            'ri-id-card-line' => ['nip', 'nik', 'jabatan'],
            'ri-pulse-line' => ['status', 'kondisi'],
            'ri-image-line' => ['judul', 'materi', 'media', 'grafis', 'gambar', 'infografis'],
        ];

        foreach ($map as $icon => $keywords) {
            foreach ($keywords as $word) {
                if (str_contains($k, $word)) {
                    return $icon;
                }
            }
        }

        return 'ri-information-line';
    }
}
