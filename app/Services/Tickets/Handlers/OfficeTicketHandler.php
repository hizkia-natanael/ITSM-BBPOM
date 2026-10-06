<?php

namespace App\Services\Tickets\Handlers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\TicketAttachmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfficeTicketHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
    ) {}

    public function handle(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_lisensi_office');
        $category = Category::resolveForService('lisensi_office');

        if ($subCategory === 'permintaan_lisensi_office') {
            $validated = $request->validate([
                'tanggal_pengajuan' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'bidang' => ['required', 'string', 'max:100'],
                'merk_perangkat' => ['required', 'string', 'max:255'],
                'kode_bmn' => ['required', 'string', 'max:100'],
                'deskripsi' => ['required', 'string', 'min:5'],
            ], [
                'bidang.required' => 'Pilih Bidang / Substansi kerja Anda.',
                'merk_perangkat.required' => 'Merk Laptop / Komputer wajib diisi.',
                'kode_bmn.required' => 'Kode BMN perangkat wajib diisi.',
                'deskripsi.required' => 'Deskripsi kendala / kebutuhan lisensi wajib diisi.',
            ]);

            $subject = "Lisensi Office ({$validated['merk_perangkat']}) - {$validated['nama']}";
            $description = "Permohonan Lisensi Resmi Microsoft Office\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Bidang / Substansi: {$validated['bidang']}\n"
                . "• Merk Perangkat: {$validated['merk_perangkat']}\n"
                . "• Kode BMN: {$validated['kode_bmn']}\n\n"
                . "Deskripsi Kendala:\n" . $validated['deskripsi'];

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'lisensi_office',
                'subcategory' => 'permintaan_lisensi_office',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);

            $ticket->officeDetail()->create([
                'sub_category' => 'permintaan_lisensi_office',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'bidang' => $validated['bidang'],
                'merk_perangkat' => $validated['merk_perangkat'],
                'kode_bmn' => $validated['kode_bmn'],
                'deskripsi' => $validated['deskripsi'],
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan Lisensi Office berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
        }

        // Subkategori Lainnya
        $validated = $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'min:5'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
        ]);

        $subject = "Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Lisensi Office: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'lisensi_office',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
        ]);

        $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

        $ticket->officeDetail()->create([
            'sub_category' => 'lainnya',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan Lisensi Office berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }
}
