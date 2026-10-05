<?php

namespace App\Services\Export;

use Symfony\Component\HttpFoundation\Response;

class TicketExportService
{
    public function __construct(
        protected TicketQueryBuilder $queryBuilder,
        protected TicketPdfExportService $pdfService,
        protected TicketExcelExportService $excelService
    ) {}

    /**
     * Export tickets according to filters and requested format.
     */
    public function export(array $filters = []): Response
    {
        $result = $this->queryBuilder->build($filters);
        $tickets = $result['tickets'];
        $summary = $result['summary'];

        $format = strtolower($filters['format'] ?? 'pdf');

        if ($format === 'excel' || $format === 'xlsx') {
            return $this->excelService->download($tickets, $summary);
        }

        return $this->pdfService->download($tickets, $summary);
    }

    /**
     * Stream tickets for in-browser PDF preview.
     */
    public function streamPdf(array $filters = []): Response
    {
        $result = $this->queryBuilder->build($filters);
        return $this->pdfService->stream($result['tickets'], $result['summary']);
    }

    /**
     * Get preview metadata and ticket count for UI feedback.
     */
    public function getPreview(array $filters = []): array
    {
        return $this->queryBuilder->build($filters);
    }
}
