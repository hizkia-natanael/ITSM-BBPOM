<?php

namespace App\Services\Export\Excel;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExcelSignatureStyler
{
    /**
     * Terapkan tanda tangan pengesahan dan audit stamp.
     */
    public static function apply(Worksheet $sheet, array $summary, int $lastRow): void
    {
        $sig = $lastRow + 2;
        $sheet->setCellValue("J{$sig}", 'Semarang, ' . now()->translatedFormat('d F Y'))
            ->getStyle("J{$sig}")->applyFromArray(['font' => ['size' => 9, 'color' => ['rgb' => '334155']]]);

        $sheet->setCellValue('J' . ($sig + 1), 'Petugas Pengelola Helpdesk TIK,')
            ->getStyle('J' . ($sig + 1))->applyFromArray(['font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '0F172A']]]);

        $sheet->setCellValue('J' . ($sig + 5), $summary['generated_by'] ?? 'Administrator ITSM')
            ->getStyle('J' . ($sig + 5))->applyFromArray([
                'font' => ['bold' => true, 'underline' => true, 'size' => 9.5, 'color' => ['rgb' => '0F172A']],
            ]);

        $nip = !empty($summary['generated_by_nip'])
            ? 'NIP. ' . $summary['generated_by_nip']
            : 'Administrator Sistem Layanan TI BBPOM di Semarang';

        $sheet->setCellValue('J' . ($sig + 6), $nip)
            ->getStyle('J' . ($sig + 6))->applyFromArray(['font' => ['size' => 8.5, 'color' => ['rgb' => '64748B']]]);

        $audit = $sig + 8;
        $sheet->mergeCells("A{$audit}:M{$audit}");
        $sheet->setCellValue("A{$audit}", "Dokumen ini digenerate secara otomatis oleh Sistem Manajemen Layanan TI (ITSM) BBPOM Semarang • Rekapitulasi Resmi Layanan");
        $sheet->getStyle("A{$audit}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '94A3B8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DASHED, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension($audit)->setRowHeight(20);
    }
}
