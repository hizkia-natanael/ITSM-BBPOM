<?php

namespace App\Services\Tickets;

use Illuminate\Http\Request;

class TicketDynamicPayloadCompiler
{
    /**
     * Parse structured markdown description (SIPT, Srikandi, Email, Internet, Subsite, Infografis, dll)
     * menjadi metadata, fields (parameter singkat), dan longFields (deskripsi / narasi panjang).
     *
     * @param string|null $desc
     * @return array{is_structured: bool, meta: array<string, string>, fields: array<string, string>, long_fields: array<string, string>}
     */
    public static function extractStructuredPayload(?string $desc): array
    {
        $desc = $desc ?: '';
        $isStructured = str_contains($desc, '**Layanan:**') || str_contains($desc, '### Rincian Kebutuhan');

        $meta = [];
        $fields = [];
        $longFields = [];

        if (!$isStructured) {
            return [
                'is_structured' => false,
                'meta' => [],
                'fields' => [],
                'long_fields' => [],
            ];
        }

        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $desc));
        $inRincian = false;
        $currentLongKey = null;
        $currentLongVal = [];
        $lastFieldKey = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '---') continue;

            if (str_starts_with($trimmed, '### Rincian Kebutuhan')) {
                $inRincian = true;
                continue;
            }

            if (!$inRincian) {
                if ($trimmed === '') continue;
                if (preg_match('/^\*\*(.+?):\*\*\s*(.*)$/', $trimmed, $m)) {
                    $meta[trim($m[1])] = trim($m[2]);
                }
            } else {
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

                    $isLong = $val === ''
                        || strlen($val) > 80
                        || (bool) preg_match('/(deskripsi|keterangan|catatan|materi|konten|berita|pesan|keluhan|alasan|perbaikan|permintaan|detail|narasi)/i', $key);

                    if ($isLong) {
                        $currentLongKey = $key;
                        $currentLongVal = $val !== '' ? [$val] : [];
                        $lastFieldKey = null;
                        unset($fields[$key]);
                    } else {
                        if (!isset($fields[$key])) {
                            $fields[$key] = $val;
                            $lastFieldKey = $key;
                        }
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
        }

        if ($currentLongKey !== null) {
            $text = trim(implode("\n", $currentLongVal));
            if ($text !== '') {
                $longFields[$currentLongKey] = $text;
            }
        }

        return [
            'is_structured' => true,
            'meta' => $meta,
            'fields' => $fields,
            'long_fields' => $longFields,
        ];
    }

    /**
     * Dapatkan ikon Remix Icon yang sesuai berdasarkan kata kunci label parameter.
     */
    public static function getFieldIcon(string $key): string
    {
        $k = strtolower($key);
        if (str_contains($k, 'user') || str_contains($k, 'nama') || str_contains($k, 'pemohon')) return 'ri-user-3-line';
        if (str_contains($k, 'pass') || str_contains($k, 'sandi')) return 'ri-lock-password-line';
        if (str_contains($k, 'sampel') || str_contains($k, 'kode')) return 'ri-test-tube-line';
        if (str_contains($k, 'email') || str_contains($k, 'mail')) return 'ri-mail-line';
        if (str_contains($k, 'domain') || str_contains($k, 'url') || str_contains($k, 'link') || str_contains($k, 'subsite')) return 'ri-global-line';
        if (str_contains($k, 'ip') || str_contains($k, 'mac') || str_contains($k, 'port') || str_contains($k, 'lan') || str_contains($k, 'wifi')) return 'ri-router-line';
        if (str_contains($k, 'telepon') || str_contains($k, 'wa') || str_contains($k, 'kontak') || str_contains($k, 'hp')) return 'ri-phone-line';
        if (str_contains($k, 'lokasi') || str_contains($k, 'ruang') || str_contains($k, 'gedung')) return 'ri-map-pin-2-line';
        if (str_contains($k, 'unit') || str_contains($k, 'seksi') || str_contains($k, 'bidang')) return 'ri-building-4-line';
        if (str_contains($k, 'acara') || str_contains($k, 'kegiatan') || str_contains($k, 'rapat')) return 'ri-calendar-event-line';
        if (str_contains($k, 'jam') || str_contains($k, 'waktu') || str_contains($k, 'tanggal') || str_contains($k, 'deadline') || str_contains($k, 'tenggat')) return 'ri-time-line';
        if (str_contains($k, 'tipe') || str_contains($k, 'kategori') || str_contains($k, 'jenis')) return 'ri-price-tag-3-line';
        if (str_contains($k, 'orientasi') || str_contains($k, 'ukuran')) return 'ri-aspect-ratio-line';
        if (str_contains($k, 'perangkat') || str_contains($k, 'laptop') || str_contains($k, 'komputer') || str_contains($k, 'pc') || str_contains($k, 'merk')) return 'ri-computer-line';
        if (str_contains($k, 'bmn')) return 'ri-barcode-line';
        if (str_contains($k, 'nip') || str_contains($k, 'nik') || str_contains($k, 'jabatan')) return 'ri-id-card-line';
        if (str_contains($k, 'status') || str_contains($k, 'kondisi')) return 'ri-pulse-line';
        if (str_contains($k, 'judul') || str_contains($k, 'materi') || str_contains($k, 'media') || str_contains($k, 'grafis') || str_contains($k, 'gambar') || str_contains($k, 'infografis')) return 'ri-image-line';
        return 'ri-information-line';
    }

    /**
     * Kompilasi data dinamis dari formulir spesifikasi layanan TIK ke format subject dan markdown description.
     *
     * @return array{0: string|null, 1: string} [generatedSubject, generatedDescription]
     */
    public function compile(Request $request, ?array $service, ?string $subKey): array
    {
        if (!$service) {
            return [$request->input('subject'), $request->input('description')];
        }

        $serviceName = $service['name'] ?? 'Layanan TI';
        $subcategories = $service['subcategories'] ?? [];
        $subLabel = $subcategories[$subKey] ?? ($request->input('custom_subcategory') ?: 'Umum');

        $lines = [];
        $lines[] = "**Layanan:** {$serviceName}";
        $lines[] = "**Sub Kategori:** {$subLabel}";
        $lines[] = "**Tanggal Pengajuan:** " . now()->format('d/m/Y H:i');
        $lines[] = "**Nama Pemohon:** " . ($request->user() ? $request->user()->name : '-');
        $lines[] = "---";
        $lines[] = "### Rincian Kebutuhan / Formulir:";

        $subjectSuffix = '';

        // 1. SIPT
        if ($service['id'] === 'sipt') {
            if ($subKey === 'reset_password') {
                $userSipt = $request->input('sipt_username', '-');
                $lines[] = "* **Username SIPT:** {$userSipt}";
                if ($reqPass = $request->input('sipt_password')) {
                    $lines[] = "* **Request Password Baru:** {$reqPass}";
                }
                $subjectSuffix = "Reset Password ({$userSipt})";
            } elseif ($subKey === 'rollback_sampel') {
                $noSampel = $request->input('sipt_sample_number', '-');
                $komoditi = $request->input('sipt_commodity', '-');
                $lines[] = "* **Nomor Sampel:** {$noSampel}";
                $lines[] = "* **Komoditi:** {$komoditi}";
                $subjectSuffix = "Rollback Sampel {$noSampel} ({$komoditi})";
            } elseif ($subKey === 'perbaikan_sampel') {
                $kdSampel = $request->input('sipt_sample_code', '-');
                $komoditi = $request->input('sipt_commodity', '-');
                $nmSampel = $request->input('sipt_sample_name', '-');
                $lines[] = "* **Kode Sampel:** {$kdSampel}";
                $lines[] = "* **Komoditi:** {$komoditi}";
                $lines[] = "* **Nama Sampel:** {$nmSampel}";
                $lines[] = "* **Sebelum Perbaikan:** " . $request->input('sipt_before_repair', '-');
                $lines[] = "* **Perbaikan:** " . $request->input('sipt_after_repair', '-');
                $subjectSuffix = "Perbaikan Sampel {$kdSampel} ({$nmSampel})";
            } elseif ($subKey === 'cek_posisi_sampel') {
                $kdSampel = $request->input('sipt_sample_code', '-');
                $komoditi = $request->input('sipt_commodity', '-');
                $nmSampel = $request->input('sipt_sample_name', '-');
                $lines[] = "* **Kode Sampel:** {$kdSampel}";
                $lines[] = "* **Komoditi:** {$komoditi}";
                $lines[] = "* **Nama Sampel:** {$nmSampel}";
                $subjectSuffix = "Cek Posisi Sampel {$kdSampel}";
            } else {
                $lines[] = "* **Deskripsi:** " . trim((string) $request->input('service_description', $request->input('description', '-')));
                $subjectSuffix = "Kendala SIPT";
            }
        }

        // 2. Srikandi
        elseif ($service['id'] === 'srikandi') {
            if ($subKey === 'reset_password') {
                $userSrikandi = $request->input('srikandi_username', '-');
                $lines[] = "* **Username Srikandi:** {$userSrikandi}";
                if ($reqPass = $request->input('srikandi_password')) {
                    $lines[] = "* **Request Password Baru:** {$reqPass}";
                }
                $subjectSuffix = "Reset Password ({$userSrikandi})";
            } elseif ($subKey === 'buat_akun') {
                $nama = $request->input('srikandi_fullname', '-');
                $lines[] = "* **Nama Lengkap:** {$nama}";
                $lines[] = "* **NIK:** " . $request->input('srikandi_nik', '-');
                $lines[] = "* **No. Telp / WhatsApp:** " . $request->input('srikandi_phone', '-');
                $lines[] = "* **Alamat Email:** " . $request->input('srikandi_email', '-');
                $lines[] = "* **Request Username:** " . $request->input('srikandi_req_username', '-');
                if ($reqPass = $request->input('srikandi_req_password')) {
                    $lines[] = "* **Request Password Awal:** {$reqPass}";
                }
                $subjectSuffix = "Pembuatan Akun Baru ({$nama})";
            } elseif ($subKey === 'nonaktif_akun') {
                $userSrikandi = $request->input('srikandi_username', '-');
                $lines[] = "* **Username Srikandi:** {$userSrikandi}";
                $lines[] = "* **Alasan Dinonaktifkan:** " . $request->input('srikandi_disable_reason', '-');
                $subjectSuffix = "Penonaktifan Akun ({$userSrikandi})";
            } elseif ($subKey === 'reset_mfa') {
                $userSrikandi = $request->input('srikandi_username', '-');
                $lines[] = "* **Username Srikandi:** {$userSrikandi}";
                $lines[] = "* **Permintaan:** Reset MFA (Multi-Factor Authentication)";
                $subjectSuffix = "Reset MFA ({$userSrikandi})";
            } else {
                $lines[] = "* **Deskripsi:** " . trim((string) $request->input('service_description', $request->input('description', '-')));
                $subjectSuffix = "Kendala Srikandi";
            }
        }

        // 3. Email Corporate
        elseif ($service['id'] === 'email_corporate') {
            if ($subKey === 'reset_password') {
                $nip = $request->input('email_nip', '-');
                $lines[] = "* **NIP:** {$nip}";
                if ($reqPass = $request->input('email_password')) {
                    $lines[] = "* **Request Password Baru:** {$reqPass}";
                }
                $subjectSuffix = "Reset Password (NIP: {$nip})";
            } else {
                $lines[] = "* **Deskripsi:** " . trim((string) $request->input('service_description', $request->input('description', '-')));
                $subjectSuffix = "Kendala Email Corporate";
            }
        }

        // 7. Internet & Jaringan
        elseif ($service['id'] === 'internet') {
            if ($subKey === 'lemot_mati') {
                $loc = $request->input('ap_location', '-');
                $lines[] = "* **Lokasi Titik Access Point / Ruangan:** {$loc}";
                $lines[] = "* **Keluhan:** " . trim((string) $request->input('complaint_details', '-'));
                $subjectSuffix = "Keluhan Internet Lemot / Mati ({$loc})";
            } elseif ($subKey === 'tambah_ap') {
                $loc = $request->input('ap_location', '-');
                $lines[] = "* **Request Lokasi Access Point:** {$loc}";
                $lines[] = "* **Keluhan / Alasan Kebutuhan:** " . trim((string) $request->input('complaint_details', '-'));
                $subjectSuffix = "Permintaan Titik AP Baru ({$loc})";
            } else {
                $lines[] = "* **Deskripsi:** " . trim((string) $request->input('service_description', $request->input('description', '-')));
                $subjectSuffix = "Kendala Jaringan & Internet";
            }
        }

        // 8. Subsite
        elseif ($service['id'] === 'subsite') {
            if ($subKey === 'upload_berita') {
                $judul = trim($request->input('news_title', '-'));
                $lines[] = "* **Judul Berita:** {$judul}";
                $lines[] = "* **Isi Berita:** " . trim($request->input('news_content', '-'));
                if ($tgl = $request->input('target_date')) {
                    $lines[] = "* **Request Tanggal Upload:** {$tgl}";
                }
                $subjectSuffix = "Upload Berita: {$judul}";
            } elseif ($subKey === 'upload_dokumen') {
                $doc = trim($request->input('doc_name', '-'));
                $loc = trim($request->input('doc_location', '-'));
                $lines[] = "* **Nama Dokumen:** {$doc}";
                $lines[] = "* **Lokasi Upload / Kategori:** {$loc}";
                $subjectSuffix = "Upload Dokumen: {$doc}";
            } elseif ($subKey === 'hapus_dokumen') {
                $doc = trim($request->input('doc_name', '-'));
                $lines[] = "* **Judul Dokumen yang Akan Dihapus:** {$doc}";
                $lines[] = "* **Link Dokumen:** " . trim($request->input('doc_url', '-'));
                $subjectSuffix = "Hapus Dokumen: {$doc}";
            }
        }

        // 9. Infografis
        elseif ($service['id'] === 'infografis') {
            $judul = trim($request->input('infographic_title', '-'));
            $lines[] = "* **Judul Infografis:** {$judul}";
            $lines[] = "* **Isi Materi / Pesan Utama:** " . trim($request->input('infographic_content', '-'));
            $subjectSuffix = "Pembuatan Infografis: {$judul}";
        }

        // Fallback / Other services (4, 5, 6, 10, 11, 12)
        else {
            $desc = trim($request->input('description', $request->input('service_description', '-')));
            $lines[] = "* **Deskripsi:** {$desc}";
            $subjectSuffix = $request->input('subject', $subLabel);
        }

        $generatedSubject = $subjectSuffix ?: $subLabel;
        $generatedDescription = implode("\n", $lines);

        return [
            $request->input('subject') ?: $generatedSubject,
            $generatedDescription,
        ];
    }
}
