<?php

namespace App\Services\Tickets\Handlers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\TicketAttachmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ZoomTicketHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
    ) {}

    public function handle(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_link_zoom');

        if ($subCategory === 'permintaan_link_zoom') {
            $validated = $request->validate([
                'tanggal_pengajuan' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'nama_acara' => ['required', 'string', 'max:255'],
                'jam' => ['required', 'string', 'max:100'],
            ], [
                'nama_acara.required' => 'Nama acara rapat / webinar wajib diisi.',
                'jam.required' => 'Jam / waktu pelaksanaan acara wajib diisi.',
            ]);

            $subject = "Permintaan Link Zoom - {$validated['nama_acara']}";
            $description = "Permohonan Layanan Akun Zoom Meeting / Webinar\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Nama Pemohon: {$validated['nama']}\n"
                . "• Nama Acara: {$validated['nama_acara']}\n"
                . "• Jam / Waktu Pelaksanaan: {$validated['jam']}";

            $category = Category::resolveForService('zoom');

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'zoom',
                'subcategory' => 'permintaan_link_zoom',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);

            $ticket->zoomDetail()->create([
                'sub_category' => 'permintaan_link_zoom',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'nama_acara' => $validated['nama_acara'],
                'jam' => $validated['jam'],
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan link Zoom berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
        }

        // Subkategori Lainnya
        $validated = $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'min:5'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt,xls,xlsx,csv,webp', 'max:102400'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt,xls,xlsx,csv,webp', 'max:102400'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt,xls,xlsx,csv,webp', 'max:102400'],
        ]);

        $subject = "Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Zoom: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Nama Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala: " . trim($validated['deskripsi']);

        $category = Category::resolveForService('zoom');

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'zoom',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
        ]);

        $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

        $ticket->zoomDetail()->create([
            'sub_category' => 'lainnya',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'deskripsi' => trim($validated['deskripsi']),
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan Zoom berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }
}
