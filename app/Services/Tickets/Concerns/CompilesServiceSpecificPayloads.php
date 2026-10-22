<?php

namespace App\Services\Tickets\Concerns;

use Illuminate\Http\Request;

trait CompilesServiceSpecificPayloads
{
    private function compileSipt(Request $r, ?string $sub, array &$lines): string
    {
        if ($sub === 'reset_password') {
            $lines[] = "* **Username SIPT:** " . ($u = $r->input('sipt_username', '-'));
            if ($p = $r->input('sipt_password')) $lines[] = "* **Request Password Baru:** {$p}";
            return "Reset Password ({$u})";
        }
        if ($sub === 'rollback_sampel') {
            array_push($lines, "* **Nomor Sampel:** " . ($no = $r->input('sipt_sample_number', '-')), "* **Komoditi:** " . ($kom = $r->input('sipt_commodity', '-')));
            return "Rollback Sampel {$no} ({$kom})";
        }
        if ($sub === 'perbaikan_sampel') {
            array_push($lines, "* **Kode Sampel:** " . ($kd = $r->input('sipt_sample_code', '-')), "* **Komoditi:** " . $r->input('sipt_commodity', '-'), "* **Nama Sampel:** " . ($nm = $r->input('sipt_sample_name', '-')), "* **Sebelum Perbaikan:** " . $r->input('sipt_before_repair', '-'), "* **Perbaikan:** " . $r->input('sipt_after_repair', '-'));
            return "Perbaikan Sampel {$kd} ({$nm})";
        }
        if ($sub === 'cek_posisi_sampel') {
            array_push($lines, "* **Kode Sampel:** " . ($kd = $r->input('sipt_sample_code', '-')), "* **Komoditi:** " . $r->input('sipt_commodity', '-'), "* **Nama Sampel:** " . $r->input('sipt_sample_name', '-'));
            return "Cek Posisi Sampel {$kd}";
        }
        $lines[] = "* **Deskripsi:** " . trim((string) $r->input('service_description', $r->input('description', '-')));
        return "Kendala SIPT";
    }

    private function compileSrikandi(Request $r, ?string $sub, array &$lines): string
    {
        $u = $r->input('srikandi_username', '-');
        if ($sub === 'reset_password') {
            $lines[] = "* **Username Srikandi:** {$u}";
            if ($p = $r->input('srikandi_password')) $lines[] = "* **Request Password Baru:** {$p}";
            return "Reset Password ({$u})";
        }
        if ($sub === 'buat_akun') {
            array_push($lines, "* **Nama Lengkap:** " . ($nm = $r->input('srikandi_fullname', '-')), "* **NIK:** " . $r->input('srikandi_nik', '-'), "* **No. Telp / WhatsApp:** " . $r->input('srikandi_phone', '-'), "* **Alamat Email:** " . $r->input('srikandi_email', '-'), "* **Request Username:** " . $r->input('srikandi_req_username', '-'));
            if ($p = $r->input('srikandi_req_password')) $lines[] = "* **Request Password Awal:** {$p}";
            return "Pembuatan Akun Baru ({$nm})";
        }
        if ($sub === 'nonaktif_akun') {
            array_push($lines, "* **Username Srikandi:** {$u}", "* **Alasan Dinonaktifkan:** " . $r->input('srikandi_disable_reason', '-'));
            return "Penonaktifan Akun ({$u})";
        }
        if ($sub === 'reset_mfa') {
            array_push($lines, "* **Username Srikandi:** {$u}", "* **Permintaan:** Reset MFA (Multi-Factor Authentication)");
            return "Reset MFA ({$u})";
        }
        $lines[] = "* **Deskripsi:** " . trim((string) $r->input('service_description', $r->input('description', '-')));
        return "Kendala Srikandi";
    }

    private function compileEmail(Request $r, ?string $sub, array &$lines): string
    {
        if ($sub === 'reset_password') {
            $lines[] = "* **NIP:** " . ($nip = $r->input('email_nip', '-'));
            if ($p = $r->input('email_password')) $lines[] = "* **Request Password Baru:** {$p}";
            return "Reset Password (NIP: {$nip})";
        }
        $lines[] = "* **Deskripsi:** " . trim((string) $r->input('service_description', $r->input('description', '-')));
        return "Kendala Email Corporate";
    }

    private function compileInternet(Request $r, ?string $sub, array &$lines): string
    {
        $loc = $r->input('ap_location', '-');
        if ($sub === 'lemot_mati' || $sub === 'tambah_ap') {
            $lbl = $sub === 'lemot_mati' ? ['Lokasi Titik Access Point / Ruangan', 'Keluhan', "Keluhan Internet Lemot / Mati ({$loc})"] : ['Request Lokasi Access Point', 'Keluhan / Alasan Kebutuhan', "Permintaan Titik AP Baru ({$loc})"];
            array_push($lines, "* **{$lbl[0]}:** {$loc}", "* **{$lbl[1]}:** " . trim((string) $r->input('complaint_details', '-')));
            return $lbl[2];
        }
        $lines[] = "* **Deskripsi:** " . trim((string) $r->input('service_description', $r->input('description', '-')));
        return "Kendala Jaringan & Internet";
    }

    private function compileSubsite(Request $r, ?string $sub, array &$lines): string
    {
        if ($sub === 'upload_berita') {
            array_push($lines, "* **Judul Berita:** " . ($j = trim($r->input('news_title', '-'))), "* **Isi Berita:** " . trim($r->input('news_content', '-')));
            if ($t = $r->input('target_date')) $lines[] = "* **Request Tanggal Upload:** {$t}";
            return "Upload Berita: {$j}";
        }
        $d = trim($r->input('doc_name', '-'));
        if ($sub === 'upload_dokumen') {
            array_push($lines, "* **Nama Dokumen:** {$d}", "* **Lokasi Upload / Kategori:** " . trim($r->input('doc_location', '-')));
            return "Upload Dokumen: {$d}";
        }
        if ($sub === 'hapus_dokumen') {
            array_push($lines, "* **Judul Dokumen yang Akan Dihapus:** {$d}", "* **Link Dokumen:** " . trim($r->input('doc_url', '-')));
            return "Hapus Dokumen: {$d}";
        }
        return '';
    }

    private function compileInfografis(Request $r, array &$lines): string
    {
        array_push($lines, "* **Judul Infografis:** " . ($j = trim($r->input('infographic_title', '-'))), "* **Isi Materi / Pesan Utama:** " . trim($r->input('infographic_content', '-')));
        return "Pembuatan Infografis: {$j}";
    }

    private function compileDefault(Request $r, string $subLabel, array &$lines): string
    {
        $lines[] = "* **Deskripsi:** " . trim($r->input('description', $r->input('service_description', '-')));
        return (string) $r->input('subject', $subLabel);
    }
}
