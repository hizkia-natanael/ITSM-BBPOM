<?php

namespace App\Services\Export\Excel;

use App\Services\Export\TicketQueryBuilder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExcelDetailSheetBuilder
{
    /**
     * Build Sheet 1: Rekapitulasi Detail Aduan.
     */
    public static function build(Worksheet $sheet, Collection $tickets, array $summary): void
    {
        $sheet->setTitle('Rekapitulasi Aduan');
        $sheet->setShowGridLines(true);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getHeaderFooter()
            ->setOddHeader('&C&B&10BALAI BESAR PENGAWAS OBAT DAN MAKANAN DI SEMARANG')
            ->setOddFooter('&L&8Dokumen Rekapitulasi ITSM BBPOM Semarang&R&8Halaman &P dari &N');
        TicketExcelHeaderStyler::buildKop($sheet);
        TicketExcelHeaderStyler::buildMetaAndKpi($sheet, $summary);

        self::buildTableHeaders($sheet);
        $lastRow = self::buildDataRows($sheet, $tickets);

        $sheet->freezePane('A11');
        if ($tickets->isNotEmpty()) { $sheet->setAutoFilter("A10:M{$lastRow}"); }

        $widths = ['A' => 6, 'B' => 18, 'C' => 17, 'D' => 24, 'E' => 28, 'F' => 22, 'G' => 24, 'H' => 32, 'I' => 45, 'J' => 22, 'K' => 18, 'L' => 15, 'M' => 14];
        foreach ($widths as $col => $w) { $sheet->getColumnDimension($col)->setWidth($w); }

        TicketExcelSignatureStyler::apply($sheet, $summary, $lastRow);
    }

    private static function buildTableHeaders(Worksheet $sheet): void
    {
        $headers = [
            'A10' => 'No', 'B10' => 'No. Tiket', 'C10' => 'Waktu Masuk',
            'D10' => 'Nama Pelapor', 'E10' => 'Email / NIP Pelapor', 'F10' => 'Kategori Layanan',
            'G10' => 'Sub-Kategori / Detail', 'H10' => 'Subjek Kendala', 'I10' => 'Deskripsi Aduan',
            'J10' => 'Petugas IT (Teknisi)', 'K10' => 'Pembaruan Terakhir', 'L10' => 'Status Aduan',
            'M10' => 'Total Balasan',
        ];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }
        $sheet->getStyle('A10:M10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '04382A']]],
        ]);
        $sheet->getRowDimension(10)->setRowHeight(26);
    }

    private static function buildDataRows(Worksheet $sheet, Collection $tickets): int
    {
        $row = 11;
        $statusColors = [
            'selesai' => ['bg' => 'DCFCE7', 'fg' => '15803D'],
            'diproses' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
            'pending' => ['bg' => 'FEF3C7', 'fg' => 'B45309'],
            'ditolak' => ['bg' => 'FFE4E6', 'fg' => 'BE123C'],
        ];

        foreach ($tickets as $idx => $t) {
            $userNip = $t->user?->nip ? " (NIP: {$t->user->nip})" : '';
            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", $t->ticket_number);
            $sheet->setCellValue("C{$row}", $t->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("D{$row}", $t->user?->name ?? 'Anonim');
            $sheet->setCellValue("E{$row}", ($t->user?->email ?? '-') . $userNip);
            $sheet->setCellValue("F{$row}", $t->service_name);
            $sheet->setCellValue("G{$row}", $t->subcategory_label ?? '-');
            $sheet->setCellValue("H{$row}", $t->clean_subject ?? TicketQueryBuilder::stripMarkdown($t->subject));
            $sheet->setCellValue("I{$row}", $t->clean_description ?? TicketQueryBuilder::stripMarkdown($t->description));
            $sheet->setCellValue("J{$row}", $t->handledBy?->name ?? 'Belum Ditugaskan');
            $sheet->setCellValue("K{$row}", $t->updated_at->format('d/m/Y H:i'));
            $sheet->setCellValue("L{$row}", $t->status_label);
            $sheet->setCellValue("M{$row}", $t->responses->count());

            $bg = ($idx % 2 === 1) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'font' => ['size' => 9, 'color' => ['rgb' => '1E293B']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            foreach (['A', 'B', 'C', 'K', 'L', 'M'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->getColor()->setRGB('0369A1');

            $statusKey = strtolower($t->status);
            if (isset($statusColors[$statusKey])) {
                $sheet->getStyle("L{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColors[$statusKey]['bg']]],
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusColors[$statusKey]['fg']]],
                ]);
            }
            $sheet->getStyle("H{$row}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("I{$row}")->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }
        return max(11, $row - 1);
    }
}
