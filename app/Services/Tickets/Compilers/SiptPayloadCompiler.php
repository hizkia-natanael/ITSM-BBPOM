<?php

namespace App\Services\Tickets\Compilers;

use Illuminate\Http\Request;

class SiptPayloadCompiler
{
    public static function compile(Request $r, ?string $sub, array &$lines): string
    {
        if ($sub === 'reset_password') {
            $u = $r->input('sipt_username', '-');
            $lines[] = "* **Username SIPT:** {$u}";
            if ($p = $r->input('sipt_password')) {
                $lines[] = "* **Request Password Baru:** {$p}";
            }

            return "Reset Password ({$u})";
        }

        if ($sub === 'rollback_sampel') {
            $no = $r->input('sipt_sample_number', '-');
            $kom = $r->input('sipt_commodity', '-');
            array_push($lines, "* **Nomor Sampel:** {$no}", "* **Komoditi:** {$kom}");

            return "Rollback Sampel {$no} ({$kom})";
        }

        if ($sub === 'perbaikan_sampel') {
            $kd = $r->input('sipt_sample_code', '-');
            $nm = $r->input('sipt_sample_name', '-');
            array_push(
                $lines,
                "* **Kode Sampel:** {$kd}",
                '* **Komoditi:** ' . $r->input('sipt_commodity', '-'),
                "* **Nama Sampel:** {$nm}",
                '* **Sebelum Perbaikan:** ' . $r->input('sipt_before_repair', '-'),
                '* **Perbaikan:** ' . $r->input('sipt_after_repair', '-')
            );

            return "Perbaikan Sampel {$kd} ({$nm})";
        }

        if ($sub === 'cek_posisi_sampel') {
            $kd = $r->input('sipt_sample_code', '-');
            array_push(
                $lines,
                "* **Kode Sampel:** {$kd}",
                '* **Komoditi:** ' . $r->input('sipt_commodity', '-'),
                '* **Nama Sampel:** ' . $r->input('sipt_sample_name', '-')
            );

            return "Cek Posisi Sampel {$kd}";
        }

        if ($sub === 'hapus_sampel') {
            $no = trim((string) $r->input('sipt_sample_number', '')) ?: '-';
            $kom = trim((string) $r->input('sipt_commodity', '')) ?: '-';
            array_push($lines, "* **Nomor Sampel:** {$no}", "* **Komoditi:** {$kom}");

            return "Hapus Sampel {$no} ({$kom})";
        }

        if ($sub === 'hapus_sarana') {
            $id = trim((string) $r->input('sipt_facility_id', '')) ?: '-';
            $nm = trim((string) $r->input('sipt_facility_name', '')) ?: '-';
            array_push($lines, "* **No. ID Sarana:** {$id}", "* **Nama Sarana:** {$nm}");

            return "Hapus Sarana {$nm} (ID: {$id})";
        }

        $lines[] = '* **Deskripsi:** ' . trim((string) $r->input('service_description', $r->input('description', '-')));

        return 'Kendala SIPT';
    }
}
