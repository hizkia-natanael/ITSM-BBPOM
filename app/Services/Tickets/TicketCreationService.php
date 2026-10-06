<?php

namespace App\Services\Tickets;

use App\Models\Category;
use App\Services\Tickets\Handlers\BsreTicketHandler;
use App\Services\Tickets\Handlers\KegiatanTicketHandler;
use App\Services\Tickets\Handlers\LainnyaTicketHandler;
use App\Services\Tickets\Handlers\OfficeTicketHandler;
use App\Services\Tickets\Handlers\StandardTicketHandler;
use App\Services\Tickets\Handlers\WindowsTicketHandler;
use App\Services\Tickets\Handlers\ZoomTicketHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketCreationService
{
    public function __construct(
        protected BsreTicketHandler $bsreHandler,
        protected ZoomTicketHandler $zoomHandler,
        protected KegiatanTicketHandler $kegiatanHandler,
        protected WindowsTicketHandler $windowsHandler,
        protected OfficeTicketHandler $officeHandler,
        protected LainnyaTicketHandler $lainnyaHandler,
        protected StandardTicketHandler $standardHandler,
    ) {}

    /**
     * Dispatch penyimpanan tiket berdasarkan service_key.
     */
    public function store(Request $request): RedirectResponse
    {
        $serviceKey = $request->input('service_key');

        return match ($serviceKey) {
            'sertifikat_bse' => $this->bsreHandler->handle($request),
            'zoom' => $this->zoomHandler->handle($request),
            'formulir_kegiatan' => $this->kegiatanHandler->handle($request),
            'install_windows' => $this->windowsHandler->handle($request),
            'lisensi_office' => $this->officeHandler->handle($request),
            'lainnya' => $this->lainnyaHandler->handle($request),
            default => $this->standardHandler->handle($request),
        };
    }

    /**
     * Resolusi ID Kategori secara aman.
     */
    public function resolveCategoryId(Request $request, string $defaultSlug = 'aplikasi-software', ?string $serviceKey = null): int
    {
        $catId = $request->input('category_id');
        if ($catId && Category::where('id', $catId)->exists()) {
            return (int) $catId;
        }

        if ($serviceKey) {
            $cat = Category::resolveForService($serviceKey);
            if ($cat) {
                return (int) $cat->id;
            }
        }

        $cat = Category::where('slug', $defaultSlug)->first()
            ?? Category::where('slug', 'aplikasi-software')->first()
            ?? Category::first();

        return $cat ? (int) $cat->id : 1;
    }
}
