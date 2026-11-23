<?php

namespace App\Services\Tickets\Handlers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\Handlers\Kegiatan\KegiatanEvaluationHandler;
use App\Services\Tickets\Handlers\Kegiatan\KegiatanPresenceHandler;
use App\Services\Tickets\TicketAttachmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KegiatanTicketHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
        protected ?KegiatanEvaluationHandler $evaluationHandler = null,
        protected ?KegiatanPresenceHandler $presenceHandler = null,
    ) {
        $this->evaluationHandler = $evaluationHandler ?? new KegiatanEvaluationHandler($attachmentService);
        $this->presenceHandler = $presenceHandler ?? new KegiatanPresenceHandler();
    }

    public function handle(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_daftar_hadir');
        $category = Category::resolveForService('formulir_kegiatan');

        if ($subCategory === 'survey') {
            return $this->evaluationHandler->handleSurvey($request, $category);
        }
        if ($subCategory === 'post_test_pretest') {
            return $this->evaluationHandler->handleTest($request, $category);
        }
        if ($subCategory === 'permintaan_daftar_hadir') {
            return $this->presenceHandler->handleDaftarHadir($request, $category);
        }
        if ($subCategory === 'permintaan_qrcode') {
            return $this->presenceHandler->handleQrCode($request, $category);
        }

        return $this->handleLainnya($request, $category);
    }

    protected function handleLainnya(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'min:5'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
        ]);

        $description = "Permohonan Layanan Formulir & Kegiatan: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\nDeskripsi Kebutuhan/Kendala: " . trim($validated['deskripsi']);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'formulir_kegiatan',
            'subcategory' => 'lainnya',
            'subject' => "Layanan Lainnya - {$validated['nama']}",
            'description' => $description,
            'status' => 'pending',
        ]);

        $ticket->kegiatanDetail()->create([
            'sub_category' => 'lainnya',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'deskripsi' => trim($validated['deskripsi']),
            'file_lampiran' => $this->attachmentService->handleUploadedAttachments($ticket, $request),
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan Formulir & Kegiatan berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }
}
