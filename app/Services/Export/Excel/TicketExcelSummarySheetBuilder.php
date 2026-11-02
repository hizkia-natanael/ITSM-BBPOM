<?php

namespace App\Services\Export\Excel;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class TicketExcelSummarySheetBuilder
{
    /**
     * Build Sheet 2: Ringkasan Eksekutif 12 Katalog.
     */
    public static function build(Spreadsheet $spreadsheet, Collection $tickets): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Ringkasan 12 Katalog');
        $sheet->setShowGridLines(true);

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $sheet->getHeaderFooter()
            ->setOddHeader('&C&B&10BALAI BESAR PENGAWAS OBAT DAN MAKANAN DI SEMARANG')
            ->setOddFooter('&L&8Dokumen Rekapitulasi ITSM BBPOM Semarang&R&8Halaman &P dari &N');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'RINGKASAN REKAPITULASI PER 12 KATALOG LAYANAN TIK');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $catHeaders = [
            'A3' => 'No', 'B3' => 'Katalog Layanan TIK', 'C3' => 'Total Aduan',
            'D3' => 'Selesai', 'E3' => 'Diproses', 'F3' => 'Pending', 'G3' => 'SLA Rate (%)',
        ];
        foreach ($catHeaders as $c => $t) {
            $sheet->setCellValue($c, $t);
        }
        $sheet->getStyle('A3:G3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        $servicesConfig = config('services_gateway.services', []);
        $catRow = 4;
        $no = 1;
        foreach ($servicesConfig as $srvKey => $srvData) {
            $matching = $tickets->filter(fn ($t) => ($t->service_key_resolved ?? $t->service_key) === $srvKey);
            $cTotal = $matching->count();
            $cSelesai = $matching->where('status', 'selesai')->count();
            $cDiproses = $matching->where('status', 'diproses')->count();
            $cPending = $matching->where('status', 'pending')->count();
            $cSla = $cTotal > 0 ? round(($cSelesai / $cTotal) * 100, 1) : 0;

            $sheet->setCellValue("A{$catRow}", $no);
            $sheet->setCellValue("B{$catRow}", $srvData['name']);
            $sheet->setCellValue("C{$catRow}", $cTotal);
            $sheet->setCellValue("D{$catRow}", $cSelesai);
            $sheet->setCellValue("E{$catRow}", $cDiproses);
            $sheet->setCellValue("F{$catRow}", $cPending);
            $sheet->setCellValue("G{$catRow}", $cSla . '%');

            $sheet->getStyle("A{$catRow}:G{$catRow}")->applyFromArray([
                'font' => ['size' => 9],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("A{$catRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$catRow}:G{$catRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $catRow++;
            $no++;
        }

        $widths = ['A' => 6, 'B' => 30, 'C' => 14, 'D' => 12, 'E' => 12, 'F' => 12, 'G' => 14];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
    }
}
