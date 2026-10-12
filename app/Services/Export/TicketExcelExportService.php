<?php

namespace App\Services\Export;

use App\Services\Export\Excel\TicketExcelDetailSheetBuilder;
use App\Services\Export\Excel\TicketExcelSummarySheetBuilder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

        // Sheet 1: Detail Aduan
        TicketExcelDetailSheetBuilder::build($spreadsheet->getActiveSheet(), $tickets, $summary);

        // Sheet 2: Ringkasan 12 Katalog
        TicketExcelSummarySheetBuilder::build($spreadsheet, $tickets);

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
