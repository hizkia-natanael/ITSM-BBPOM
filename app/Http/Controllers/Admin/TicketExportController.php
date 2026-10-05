<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Export\TicketExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketExportController extends Controller
{
    public function __construct(
        protected TicketExportService $exportService
    ) {}

    /**
     * Handle export request for tickets in PDF or Excel format.
     */
    public function export(Request $request): Response
    {
        // Pertahanan berlapis: pastikan pengguna yang login adalah admin
        abort_unless(
            $request->user()?->isAdmin(),
            403,
            'Akses ditolak. Hanya administrator Layanan TIK yang berwenang mengekspor rekapitulasi aduan.'
        );

        $validated = $request->validate([
            'format' => 'nullable|string|in:pdf,excel,xlsx',
            'category' => 'nullable|string|max:50',
            'period_type' => 'nullable|string|in:all,yearly,month_year,custom',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2099',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|in:all,pending,diproses,selesai,ditolak',
            'handled_by' => 'nullable|string|max:50',
            'stream' => 'nullable|boolean',
        ]);

        if (!empty($validated['stream']) && ($validated['format'] ?? 'pdf') === 'pdf') {
            return $this->exportService->streamPdf($validated);
        }

        return $this->exportService->export($validated);
    }
}
