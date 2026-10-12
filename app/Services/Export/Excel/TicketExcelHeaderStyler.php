<?php

namespace App\Services\Export\Excel;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExcelHeaderStyler
{
    /**
     * Build Kop Surat resmi BBPOM Semarang pada Sheet.
     */
    public static function buildKop(Worksheet $sheet): void
    {
        $logoPath = public_path('images/logo-bpom.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo Badan POM');
            $drawing->setDescription('Logo Badan POM');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(12);
            $drawing->setOffsetY(4);
            $drawing->setHeight(68);
            $drawing->setWorksheet($sheet);
        }

        $sheet->mergeCells('B1:M3');
        $richText = new RichText();

        $run1 = $richText->createTextRun("BALAI BESAR ");
        $run1->getFont()->setName('Trebuchet MS')->setBold(true)->setSize(13)->getColor()->setRGB('014282');

        $run2 = $richText->createTextRun("PENGAWAS OBAT DAN MAKANAN DI SEMARANG\n");
        $run2->getFont()->setName('Trebuchet MS')->setBold(true)->setSize(13)->getColor()->setRGB('04A651');

        $run3 = $richText->createTextRun("Jl. Sukun Raya No. 41A Banyumanik Semarang 50264\n");
        $run3->getFont()->setName('Arial Narrow')->setBold(false)->setSize(9.5)->getColor()->setRGB('181C18');

        $run4 = $richText->createTextRun("Telp. 024 - 7612324 (ULPK), Fax : 024-7613633\n");
        $run4->getFont()->setName('Arial Narrow')->setBold(false)->setSize(9)->getColor()->setRGB('181C18');

        $run5 = $richText->createTextRun("Email : bpom_semarang@pom.go.id ; Website : www.pom.go.id");
        $run5->getFont()->setName('Arial Narrow')->setBold(false)->setSize(9)->getColor()->setRGB('181C18');

        $sheet->setCellValue('B1', $richText);
        $sheet->getStyle('B1')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(24);
        $sheet->getRowDimension(3)->setRowHeight(26);

        $sheet->getStyle('A3:M3')->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '1261B0']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(10);
    }

    /**
     * Build Metadata & KPI blocks.
     */
    public static function buildMetaAndKpi(Worksheet $sheet, array $summary): void
    {
        $meta = ['font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '475569']]];
        $val = ['font' => ['bold' => false, 'size' => 9, 'color' => ['rgb' => '0F172A']]];

        $sheet->setCellValue('B5', 'Kategori Layanan:')->getStyle('B5')->applyFromArray($meta);
        $sheet->setCellValue('C5', $summary['category_name'])->getStyle('C5')->applyFromArray($val);
        $sheet->setCellValue('F5', 'Filter Status:')->getStyle('F5')->applyFromArray($meta);
        $sheet->setCellValue('G5', $summary['status_label'])->getStyle('G5')->applyFromArray($val);
        $sheet->setCellValue('J5', 'Waktu Ekspor:')->getStyle('J5')->applyFromArray($meta);
        $sheet->setCellValue('K5', $summary['generated_at'])->getStyle('K5')->applyFromArray($val);

        $sheet->setCellValue('B6', 'Periode Waktu:')->getStyle('B6')->applyFromArray($meta);
        $sheet->setCellValue('C6', $summary['period_label'])->getStyle('C6')->applyFromArray($val);
        $sheet->setCellValue('F6', 'Petugas IT:')->getStyle('F6')->applyFromArray($meta);
        $sheet->setCellValue('G6', $summary['handled_by_label'] ?? 'Semua Petugas IT')->getStyle('G6')->applyFromArray($val);
        $sheet->setCellValue('J6', 'Petugas Admin:')->getStyle('J6')->applyFromArray($meta);
        $sheet->setCellValue('K6', $summary['generated_by'])->getStyle('K6')->applyFromArray($val);

        $sheet->getRowDimension(5)->setRowHeight(18);
        $sheet->getRowDimension(6)->setRowHeight(18);
        $sheet->getRowDimension(7)->setRowHeight(10);

        $kpiItems = [
            ['range' => 'B8:C8', 'title' => 'Total Aduan', 'val' => $summary['total'], 'bg' => 'F1F5F9', 'fg' => '0F172A'],
            ['range' => 'D8:E8', 'title' => 'Selesai', 'val' => $summary['selesai'], 'bg' => 'DCFCE7', 'fg' => '15803D'],
            ['range' => 'F8:G8', 'title' => 'Diproses', 'val' => $summary['diproses'], 'bg' => 'E0F2FE', 'fg' => '0284C7'],
            ['range' => 'H8:I8', 'title' => 'Pending', 'val' => $summary['pending'], 'bg' => 'FEF3C7', 'fg' => 'B45309'],
            ['range' => 'J8:M8', 'title' => 'Ditolak', 'val' => $summary['ditolak'], 'bg' => 'FFE4E6', 'fg' => 'E11D48'],
        ];

        foreach ($kpiItems as $kpi) {
            $sheet->mergeCells($kpi['range']);
            $firstCell = explode(':', $kpi['range'])[0];
            $sheet->setCellValue($firstCell, "{$kpi['title']}: {$kpi['val']}");
            $sheet->getStyle($kpi['range'])->applyFromArray([
                'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => $kpi['fg']]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $kpi['bg']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
            ]);
        }
        $sheet->getRowDimension(8)->setRowHeight(24);
        $sheet->getRowDimension(9)->setRowHeight(12);
    }
}
