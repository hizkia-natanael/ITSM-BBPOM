<?php

namespace App\Services\Tickets\Handlers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\Handlers\Kegiatan\KegiatanEvaluationHandler;
use App\Services\Tickets\TicketAttachmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KegiatanTicketHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
        protected ?KegiatanEvaluationHandler $evaluationHandler = null,
    ) {
        $this->evaluationHandler = $evaluationHandler ?? new KegiatanEvaluationHandler($attachmentService);
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
            $validated = $request->validate([
                'tanggal_pengajuan' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'nama_acara' => ['required', 'string', 'max:255'],
                'jam' => ['required', 'string', 'max:100'],
            ], [
                'nama_acara.required' => 'Nama acara wajib diisi.',
                'jam.required' => 'Jam / jadwal acara wajib diisi.',
            ]);
            $description = "Permohonan Pembuatan Presensi / Daftar Hadir Online\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n• Nama Acara: {$validated['nama_acara']}\n• Jam / Waktu Pelaksanaan: {$validated['jam']}";
            $ticket = $this->createTicket($request, $category, 'permintaan_daftar_hadir', "Permintaan Daftar Hadir - {$validated['nama_acara']}", $description);
            $ticket->kegiatanDetail()->create([
                'sub_category' => 'permintaan_daftar_hadir',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'nama_acara' => $validated['nama_acara'],
                'jam' => $validated['jam'],
            ]);
            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan Daftar Hadir berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
        }
        if ($subCategory === 'permintaan_qrcode') {
            $validated = $request->validate([
                'tanggal_pengajuan' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'link_qrcode' => ['required', 'string', 'max:1000'],
            ], [
                'link_qrcode.required' => 'Tautan / Link yang akan dijadikan QR Code wajib diisi.',
            ]);
            $description = "Permohonan Generate QR Code Resmi\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n• Tautan Sasaran: {$validated['link_qrcode']}";
            $ticket = $this->createTicket($request, $category, 'permintaan_qrcode', "Permintaan QR Code - {$validated['nama']}", $description);
            $ticket->kegiatanDetail()->create([
                'sub_category' => 'permintaan_qrcode',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'link_qrcode' => $validated['link_qrcode'],
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan QR Code berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
        }

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

        $ticket = $this->createTicket($request, $category, 'lainnya', "Layanan Lainnya - {$validated['nama']}", $description);
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

    private function createTicket(Request $request, Category $category, string $sub, string $subject, string $desc): Ticket
    {
        return Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'formulir_kegiatan',
            'subcategory' => $sub,
            'subject' => $subject,
            'description' => $desc,
            'status' => 'pending',
        ]);
    }
}
