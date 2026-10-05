<?php

namespace App\Services\Export;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

        // 1. Header Instansi (Baris 1 & 2)
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'BALAI BESAR PENGAWAS OBAT DAN MAKANAN (BBPOM) DI SEMARANG');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:M2');
        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI PENGADUAN & LAYANAN TEKNOLOGI INFORMASI (ITSM)');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // 2. Metadata Block (Baris 4 - 6)
        $metaStyles = [
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '475569']],
        ];
        $valStyles = [
            'font' => ['bold' => false, 'size' => 9, 'color' => ['rgb' => '0F172A']],
        ];

        $sheet->setCellValue('B4', 'Kategori Layanan:')->getStyle('B4')->applyFromArray($metaStyles);
        $sheet->setCellValue('C4', $summary['category_name'])->getStyle('C4')->applyFromArray($valStyles);
        $sheet->setCellValue('H4', 'Waktu Ekspor:')->getStyle('H4')->applyFromArray($metaStyles);
        $sheet->setCellValue('I4', $summary['generated_at'])->getStyle('I4')->applyFromArray($valStyles);

        $sheet->setCellValue('B5', 'Periode Waktu:')->getStyle('B5')->applyFromArray($metaStyles);
        $sheet->setCellValue('C5', $summary['period_label'])->getStyle('C5')->applyFromArray($valStyles);
        $sheet->setCellValue('H5', 'Petugas Admin:')->getStyle('H5')->applyFromArray($metaStyles);
        $sheet->setCellValue('I5', $summary['generated_by'])->getStyle('I5')->applyFromArray($valStyles);

        $sheet->setCellValue('B6', 'Filter Status:')->getStyle('B6')->applyFromArray($metaStyles);
        $sheet->setCellValue('C6', $summary['status_label'])->getStyle('C6')->applyFromArray($valStyles);
        $sheet->setCellValue('E6', 'Petugas IT:')->getStyle('E6')->applyFromArray($metaStyles);
        $sheet->setCellValue('F6', $summary['handled_by_label'] ?? 'Semua Petugas IT')->getStyle('F6')->applyFromArray($valStyles);
        $sheet->setCellValue('H6', 'Kode Audit ISO:')->getStyle('H6')->applyFromArray($metaStyles);
        $sheet->setCellValue('I6', $summary['audit_id'])->getStyle('I6')->applyFromArray($valStyles);

        // 3. KPI Executive Ribbon (Baris 8)
        $kpiItems = [
            ['range' => 'B8:C8', 'title' => 'Total Aduan', 'val' => $summary['total'], 'bg' => 'F1F5F9', 'fg' => '0F172A'],
            ['range' => 'D8:E8', 'title' => 'Selesai', 'val' => $summary['selesai'], 'bg' => 'DCFCE7', 'fg' => '15803D'],
            ['range' => 'F8:G8', 'title' => 'Diproses', 'val' => $summary['diproses'], 'bg' => 'E0F2FE', 'fg' => '0284C7'],
            ['range' => 'H8:I8', 'title' => 'Pending', 'val' => $summary['pending'], 'bg' => 'FEF3C7', 'fg' => 'B45309'],
            ['range' => 'J8:K8', 'title' => 'Ditolak', 'val' => $summary['ditolak'], 'bg' => 'FFE4E6', 'fg' => 'E11D48'],
            ['range' => 'L8:M8', 'title' => 'SLA Resolusi', 'val' => $summary['completion_rate'] . '%', 'bg' => 'ECFDF5', 'fg' => '065F46'],
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

        // 4. Data Table Header (Baris 10)
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

        // ==========================================
        // SHEET 2: RINGKASAN EKSEKUTIF 12 KATALOG
        // ==========================================
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Ringkasan 12 Katalog');
        $summarySheet->setShowGridLines(true);

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
