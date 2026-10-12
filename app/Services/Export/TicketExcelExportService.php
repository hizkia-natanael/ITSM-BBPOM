<?php

namespace App\Services\Export;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketExcelExportService
{
    /**
     * Build the spreadsheet instance.
     */
    public function generate(Collection $tickets, array $summary): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('BBPOM Semarang ITSM')
            ->setLastModifiedBy($summary['generated_by'] ?? 'Administrator ITSM')
            ->setTitle('Laporan Rekapitulasi Aduan ITSM')
            ->setSubject('Rekapitulasi Layanan TIK BBPOM di Semarang')
            ->setDescription('Laporan resmi rekapitulasi aduan dan permohonan layanan TIK berstandar ISO ITSM')
            ->setCategory('Laporan Helpdesk TI');

        // ==========================================
        // SHEET 1: REKAPITULASI DETAIL TIKET
        // ==========================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekapitulasi Aduan');
        $sheet->setShowGridLines(true);

        // Pengaturan Halaman: Cetak Landscape A4 Standar BBPOM
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Header & Footer Cetak Resmi BBPOM
        $sheet->getHeaderFooter()
            ->setOddHeader('&C&B&10BALAI BESAR PENGAWAS OBAT DAN MAKANAN DI SEMARANG')
            ->setOddFooter('&L&8Dokumen Rekapitulasi ITSM BBPOM Semarang&R&8Halaman &P dari &N');

        // Drawing: Logo Badan POM Resmi di Pojok Kiri Kop Surat
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

        // 1. Header Kop Surat Resmi BBPOM Semarang (1 Large Merged Cell B1:M3 with RichText)
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
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(24);
        $sheet->getRowDimension(3)->setRowHeight(26);

        // Garis Pembatas Ganda Kop Surat (Double Border di bawah Baris 3)
        $sheet->getStyle('A3:M3')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '1261B0'],
                ],
            ],
        ]);

        // Baris 4: Spasi Pemisah
        $sheet->getRowDimension(4)->setRowHeight(10);

        // 2. Metadata Block (Baris 5 & 6, Font 9pt, Bersih dan Rapi)
        $metaStyles = [
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '475569']],
        ];
        $valStyles = [
            'font' => ['bold' => false, 'size' => 9, 'color' => ['rgb' => '0F172A']],
        ];

        // Baris 5: Kategori, Status, Waktu Ekspor
        $sheet->setCellValue('B5', 'Kategori Layanan:')->getStyle('B5')->applyFromArray($metaStyles);
        $sheet->setCellValue('C5', $summary['category_name'])->getStyle('C5')->applyFromArray($valStyles);
        $sheet->setCellValue('F5', 'Filter Status:')->getStyle('F5')->applyFromArray($metaStyles);
        $sheet->setCellValue('G5', $summary['status_label'])->getStyle('G5')->applyFromArray($valStyles);
        $sheet->setCellValue('J5', 'Waktu Ekspor:')->getStyle('J5')->applyFromArray($metaStyles);
        $sheet->setCellValue('K5', $summary['generated_at'])->getStyle('K5')->applyFromArray($valStyles);

        // Baris 6: Periode, Petugas IT, Petugas Admin
        $sheet->setCellValue('B6', 'Periode Waktu:')->getStyle('B6')->applyFromArray($metaStyles);
        $sheet->setCellValue('C6', $summary['period_label'])->getStyle('C6')->applyFromArray($valStyles);
        $sheet->setCellValue('F6', 'Petugas IT:')->getStyle('F6')->applyFromArray($metaStyles);
        $sheet->setCellValue('G6', $summary['handled_by_label'] ?? 'Semua Petugas IT')->getStyle('G6')->applyFromArray($valStyles);
        $sheet->setCellValue('J6', 'Petugas Admin:')->getStyle('J6')->applyFromArray($metaStyles);
        $sheet->setCellValue('K6', $summary['generated_by'])->getStyle('K6')->applyFromArray($valStyles);

        $sheet->getRowDimension(5)->setRowHeight(18);
        $sheet->getRowDimension(6)->setRowHeight(18);

        // Baris 7: Spasi Pemisah
        $sheet->getRowDimension(7)->setRowHeight(10);

        // 3. KPI Executive Ribbon (Baris 8, Font 9.5pt, 5 Kartu Rata Memenuhi B-M)
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

        // Baris 9: Spasi Pemisah
        $sheet->getRowDimension(9)->setRowHeight(12);

        // 4. Data Table Header (Baris 10, Font 9.5pt Bold)
        $headers = [
            'A10' => 'No',
            'B10' => 'No. Tiket',
            'C10' => 'Waktu Masuk',
            'D10' => 'Nama Pelapor',
            'E10' => 'Email / NIP Pelapor',
            'F10' => 'Kategori Layanan',
            'G10' => 'Sub-Kategori / Detail',
            'H10' => 'Subjek Kendala',
            'I10' => 'Deskripsi Aduan',
            'J10' => 'Petugas IT (Teknisi)',
            'K10' => 'Pembaruan Terakhir',
            'L10' => 'Status Aduan',
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

        // 5. Data Rows (Baris 11 dst)
        $currentRow = 11;
        $statusColors = [
            'selesai' => ['bg' => 'DCFCE7', 'fg' => '15803D'],
            'diproses' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
            'pending' => ['bg' => 'FEF3C7', 'fg' => 'B45309'],
            'ditolak' => ['bg' => 'FFE4E6', 'fg' => 'BE123C'],
        ];

        foreach ($tickets as $index => $ticket) {
            $userNip = $ticket->user?->nip ? " (NIP: {$ticket->user->nip})" : '';
            $userContact = ($ticket->user?->email ?? '-') . $userNip;

            $cleanSubject = $ticket->clean_subject ?? TicketQueryBuilder::stripMarkdown($ticket->subject);
            $cleanDesc = $ticket->clean_description ?? TicketQueryBuilder::stripMarkdown($ticket->description);

            $sheet->setCellValue("A{$currentRow}", $index + 1);
            $sheet->setCellValue("B{$currentRow}", $ticket->ticket_number);
            $sheet->setCellValue("C{$currentRow}", $ticket->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("D{$currentRow}", $ticket->user?->name ?? 'Anonim');
            $sheet->setCellValue("E{$currentRow}", $userContact);
            $sheet->setCellValue("F{$currentRow}", $ticket->service_name);
            $sheet->setCellValue("G{$currentRow}", $ticket->subcategory_label ?? '-');
            $sheet->setCellValue("H{$currentRow}", $cleanSubject);
            $sheet->setCellValue("I{$currentRow}", $cleanDesc);
            $sheet->setCellValue("J{$currentRow}", $ticket->handledBy?->name ?? 'Belum Ditugaskan');
            $sheet->setCellValue("K{$currentRow}", $ticket->updated_at->format('d/m/Y H:i'));
            $sheet->setCellValue("L{$currentRow}", $ticket->status_label);
            $sheet->setCellValue("M{$currentRow}", $ticket->responses->count());

            // Zebra Striping
            $rowBg = ($index % 2 === 1) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$currentRow}:M{$currentRow}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'font' => ['size' => 9, 'color' => ['rgb' => '1E293B']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Alignment spesifik
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('0369A1');
            $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Styling Badge Status
            $statusKey = strtolower($ticket->status);
            if (isset($statusColors[$statusKey])) {
                $sheet->getStyle("L{$currentRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColors[$statusKey]['bg']]],
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusColors[$statusKey]['fg']]],
                ]);
            }

            $sheet->getStyle("H{$currentRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("I{$currentRow}")->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($currentRow)->setRowHeight(24);

            $currentRow++;
        }

        $lastRow = max(11, $currentRow - 1);

        // 6. Freeze Panes & Auto Filter
        $sheet->freezePane('A11');
        if ($tickets->isNotEmpty()) {
            $sheet->setAutoFilter("A10:M{$lastRow}");
        }

        // 7. Width Otomatis
        $columnWidths = [
            'A' => 6,
            'B' => 18,
            'C' => 17,
            'D' => 24,
            'E' => 28,
            'F' => 22,
            'G' => 24,
            'H' => 32,
            'I' => 45,
            'J' => 22,
            'K' => 18,
            'L' => 15,
            'M' => 14,
        ];
        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // 8. Lembar Pengesahan Resmi Petugas Helpdesk TIK
        $sigStart = $lastRow + 2;
        $sheet->setCellValue("J{$sigStart}", 'Semarang, ' . now()->translatedFormat('d F Y'));
        $sheet->getStyle("J{$sigStart}")->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '334155']],
        ]);

        $sigRole = $sigStart + 1;
        $sheet->setCellValue("J{$sigRole}", 'Petugas Pengelola Helpdesk TIK,');
        $sheet->getStyle("J{$sigRole}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '0F172A']],
        ]);

        $sigName = $sigRole + 4;
        $sheet->setCellValue("J{$sigName}", $summary['generated_by'] ?? 'Administrator ITSM');
        $sheet->getStyle("J{$sigName}")->applyFromArray([
            'font' => ['bold' => true, 'underline' => true, 'size' => 9.5, 'color' => ['rgb' => '0F172A']],
        ]);

        $sigNip = $sigName + 1;
        $nipLabel = !empty($summary['generated_by_nip']) ? 'NIP. ' . $summary['generated_by_nip'] : 'Administrator Sistem Layanan TI BBPOM di Semarang';
        $sheet->setCellValue("J{$sigNip}", $nipLabel);
        $sheet->getStyle("J{$sigNip}")->applyFromArray([
            'font' => ['size' => 8.5, 'color' => ['rgb' => '64748B']],
        ]);

        // 9. Catatan Jejak Rekapitulasi Resmi
        $auditRow = $sigNip + 2;
        $sheet->mergeCells("A{$auditRow}:M{$auditRow}");
        $sheet->setCellValue("A{$auditRow}", "Dokumen ini digenerate secara otomatis oleh Sistem Manajemen Layanan TI (ITSM) BBPOM Semarang • Rekapitulasi Resmi Layanan");
        $sheet->getStyle("A{$auditRow}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '94A3B8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DASHED, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension($auditRow)->setRowHeight(20);

        // ==========================================
        // SHEET 2: RINGKASAN EKSEKUTIF 12 KATALOG
        // ==========================================
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Ringkasan 12 Katalog');
        $summarySheet->setShowGridLines(true);

        // Pengaturan Halaman: Cetak Landscape A4 Standar BBPOM
        $summarySheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $summarySheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $summarySheet->getPageSetup()->setFitToWidth(1);
        $summarySheet->getPageSetup()->setFitToHeight(0);

        // Header & Footer Cetak Resmi BBPOM
        $summarySheet->getHeaderFooter()
            ->setOddHeader('&C&B&10BALAI BESAR PENGAWAS OBAT DAN MAKANAN DI SEMARANG')
            ->setOddFooter('&L&8Dokumen Rekapitulasi ITSM BBPOM Semarang&R&8Halaman &P dari &N');

        $summarySheet->mergeCells('A1:G1');
        $summarySheet->setCellValue('A1', 'RINGKASAN REKAPITULASI PER 12 KATALOG LAYANAN TIK');
        $summarySheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $summarySheet->getRowDimension(1)->setRowHeight(26);

        $catHeaders = [
            'A3' => 'No',
            'B3' => 'Katalog Layanan TIK',
            'C3' => 'Total Aduan',
            'D3' => 'Selesai',
            'E3' => 'Diproses',
            'F3' => 'Pending',
            'G3' => 'SLA Rate (%)',
        ];
        foreach ($catHeaders as $c => $t) {
            $summarySheet->setCellValue($c, $t);
        }
        $summarySheet->getStyle('A3:G3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $summarySheet->getRowDimension(3)->setRowHeight(22);

        $servicesConfig = config('services_gateway.services', []);
        $catRow = 4;
        $no = 1;
        foreach ($servicesConfig as $srvKey => $srvData) {
            $matchingTickets = $tickets->filter(function ($t) use ($srvKey) {
                return $t->service_key_resolved === $srvKey || $t->service_key === $srvKey;
            });
            $cTotal = $matchingTickets->count();
            $cSelesai = $matchingTickets->where('status', 'selesai')->count();
            $cDiproses = $matchingTickets->where('status', 'diproses')->count();
            $cPending = $matchingTickets->where('status', 'pending')->count();
            $cSla = $cTotal > 0 ? round(($cSelesai / $cTotal) * 100, 1) : 0;

            $summarySheet->setCellValue("A{$catRow}", $no);
            $summarySheet->setCellValue("B{$catRow}", $srvData['name']);
            $summarySheet->setCellValue("C{$catRow}", $cTotal);
            $summarySheet->setCellValue("D{$catRow}", $cSelesai);
            $summarySheet->setCellValue("E{$catRow}", $cDiproses);
            $summarySheet->setCellValue("F{$catRow}", $cPending);
            $summarySheet->setCellValue("G{$catRow}", $cSla . '%');

            $summarySheet->getStyle("A{$catRow}:G{$catRow}")->applyFromArray([
                'font' => ['size' => 9],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $summarySheet->getStyle("A{$catRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $summarySheet->getStyle("C{$catRow}:G{$catRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $catRow++;
            $no++;
        }

        $summarySheet->getColumnDimension('A')->setWidth(6);
        $summarySheet->getColumnDimension('B')->setWidth(30);
        $summarySheet->getColumnDimension('C')->setWidth(14);
        $summarySheet->getColumnDimension('D')->setWidth(12);
        $summarySheet->getColumnDimension('E')->setWidth(12);
        $summarySheet->getColumnDimension('F')->setWidth(12);
        $summarySheet->getColumnDimension('G')->setWidth(14);

        // Kembali ke Sheet 1 sebagai default aktif
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Download the spreadsheet as StreamedResponse.
     */
    public function download(Collection $tickets, array $summary): StreamedResponse
    {
        $spreadsheet = $this->generate($tickets, $summary);
        $filename = $this->getFilename($summary);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Get filename for Excel export.
     */
    public function getFilename(array $summary): string
    {
        $cat = $summary['category_slug'] ?? 'Semua_Layanan';
        $period = $summary['period_slug'] ?? 'Semua_Waktu';
        $timestamp = now()->format('Ymd_His');

        return "ITSM_Rekap_Aduan_{$cat}_{$period}_{$timestamp}.xlsx";
    }
}
