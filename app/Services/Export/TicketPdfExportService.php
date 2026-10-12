<?php

namespace App\Services\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class TicketPdfExportService
{
    /**
     * Generate DomPDF instance for the given tickets and summary.
     */
    public function generate(Collection $tickets, array $summary): \Barryvdh\DomPDF\PDF
    {
        $logoPath = public_path('images/logo-bpom.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        $kopHeaderPath = public_path('images/kop-bbpom-semarang.png');
        $kopHeaderBase64 = file_exists($kopHeaderPath) ? base64_encode(file_get_contents($kopHeaderPath)) : '';

        $kopFooterPath = public_path('images/footer-bbpom-semarang.jpg');
        $kopFooterBase64 = file_exists($kopFooterPath) ? base64_encode(file_get_contents($kopFooterPath)) : '';

        $pdf = Pdf::loadView('exports.tickets-pdf', [
            'tickets' => $tickets,
            'summary' => $summary,
            'logoBase64' => $logoBase64,
            'kopHeaderBase64' => $kopHeaderBase64,
            'kopFooterBase64' => $kopFooterBase64,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'dpi' => 110,
        ]);

        // Add page numbering via DomPDF Canvas (above the footer wave)
        $pdf->render();
        $canvas = $pdf->getCanvas();
        $font = $pdf->getDomPDF()->getFontMetrics()->get_font("Helvetica", "normal");
        $canvas->page_text(740, 545, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 7.5, [0.35, 0.35, 0.35]);

        return $pdf;
    }

    /**
     * Download the PDF as a binary response.
     */
    public function download(Collection $tickets, array $summary): Response
    {
        $pdf = $this->generate($tickets, $summary);
        $filename = $this->getFilename($summary);

        return $pdf->download($filename);
    }

    /**
     * Stream the PDF directly in browser.
     */
    public function stream(Collection $tickets, array $summary): Response
    {
        $pdf = $this->generate($tickets, $summary);
        $filename = $this->getFilename($summary);

        return $pdf->stream($filename);
    }

    /**
     * Get filename for the PDF export.
     */
    public function getFilename(array $summary): string
    {
        $cat = $summary['category_slug'] ?? 'Semua_Layanan';
        $period = $summary['period_slug'] ?? 'Semua_Waktu';
        $timestamp = now()->format('Ymd_His');

        return "ITSM_Rekap_Aduan_{$cat}_{$period}_{$timestamp}.pdf";
    }
}
