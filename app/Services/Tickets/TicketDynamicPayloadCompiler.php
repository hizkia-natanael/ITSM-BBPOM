<?php

namespace App\Services\Tickets;

use Illuminate\Http\Request;

class TicketDynamicPayloadCompiler
{
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
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
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
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
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
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
                $subjectSuffix = "Kendala Email Corporate";
            }
        }

        // 7. Internet & Jaringan
        elseif ($service['id'] === 'internet') {
            if ($subKey === 'lemot_mati') {
                $loc = $request->input('ap_location', '-');
                $lines[] = "* **Lokasi Titik Access Point / Ruangan:** {$loc}";
                $lines[] = "* **Keluhan:** " . $request->input('complaint_details', '-');
                $subjectSuffix = "Keluhan Internet Lemot / Mati ({$loc})";
            } elseif ($subKey === 'tambah_ap') {
                $loc = $request->input('ap_location', '-');
                $lines[] = "* **Request Lokasi Access Point:** {$loc}";
                $lines[] = "* **Keluhan / Alasan Kebutuhan:** " . $request->input('complaint_details', '-');
                $subjectSuffix = "Permintaan Titik AP Baru ({$loc})";
            } else {
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
                $subjectSuffix = "Kendala Jaringan & Internet";
            }
        }

        // 8. Subsite
        elseif ($service['id'] === 'subsite') {
            if ($subKey === 'upload_berita') {
                $judul = $request->input('news_title', '-');
                $lines[] = "* **Judul Berita:** {$judul}";
                $lines[] = "* **Isi Berita:**\n" . $request->input('news_content', '-');
                if ($tgl = $request->input('target_date')) {
                    $lines[] = "* **Request Tanggal Upload:** {$tgl}";
                }
                $subjectSuffix = "Upload Berita: {$judul}";
            } elseif ($subKey === 'upload_dokumen') {
                $doc = $request->input('doc_name', '-');
                $loc = $request->input('doc_location', '-');
                $lines[] = "* **Nama Dokumen:** {$doc}";
                $lines[] = "* **Lokasi Upload / Kategori:** {$loc}";
                $subjectSuffix = "Upload Dokumen: {$doc}";
            } elseif ($subKey === 'hapus_dokumen') {
                $doc = $request->input('doc_name', '-');
                $lines[] = "* **Judul Dokumen yang Akan Dihapus:** {$doc}";
                $lines[] = "* **Link Dokumen:** " . $request->input('doc_url', '-');
                $subjectSuffix = "Hapus Dokumen: {$doc}";
            }
        }

        // 9. Infografis
        elseif ($service['id'] === 'infografis') {
            $judul = $request->input('infographic_title', '-');
            $lines[] = "* **Judul Infografis:** {$judul}";
            $lines[] = "* **Isi Materi / Pesan Utama:**\n" . $request->input('infographic_content', '-');
            $subjectSuffix = "Pembuatan Infografis: {$judul}";
        }

        // Fallback / Other services (4, 5, 6, 10, 11, 12)
        else {
            $desc = $request->input('description', $request->input('service_description', '-'));
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
