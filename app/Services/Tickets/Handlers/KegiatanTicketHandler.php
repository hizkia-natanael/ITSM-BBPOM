<?php

namespace App\Services\Tickets\Handlers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\TicketAttachmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KegiatanTicketHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
    ) {}

    public function handle(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_daftar_hadir');
        $category = Category::resolveForService('formulir_kegiatan');

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

            $subject = "Permintaan Daftar Hadir - {$validated['nama_acara']}";
            $description = "Permohonan Pembuatan Presensi / Daftar Hadir Online\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Nama Acara: {$validated['nama_acara']}\n"
                . "• Jam / Waktu Pelaksanaan: {$validated['jam']}";

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'permintaan_daftar_hadir',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);

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

            $subject = "Permintaan QR Code - {$validated['nama']}";
            $description = "Permohonan Generate QR Code Resmi\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Tautan Sasaran: {$validated['link_qrcode']}";

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'permintaan_qrcode',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);

            $ticket->kegiatanDetail()->create([
                'sub_category' => 'permintaan_qrcode',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'link_qrcode' => $validated['link_qrcode'],
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan QR Code berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
        }

        if ($subCategory === 'survey') {
            $validated = $request->validate([
                'tanggal_pengajuan' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'judul_survey' => ['required', 'string', 'max:255'],
                'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
                'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
                'attachments' => ['nullable', 'array', 'max:5'],
                'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
                'pertanyaan_json' => ['nullable', 'string'],
            ], [
                'judul_survey.required' => 'Judul survei wajib diisi.',
            ]);

            $questions = [];
            if ($request->filled('pertanyaan_json')) {
                $decoded = json_decode($request->pertanyaan_json, true);
                if (is_array($decoded)) {
                    $questions = $decoded;
                }
            }

            // Jika tidak ada upload file lampiran, minimal 1 pertanyaan wajib diisi
            if (!$request->hasFile('file_lampiran') && !$request->hasFile('attachment') && !$request->hasFile('attachments') && empty($questions)) {
                return back()->withInput()->withErrors([
                    'pertanyaan_json' => 'Silakan isi minimal 1 pertanyaan survei atau unggah dokumen draf pertanyaan (PDF/Word).',
                ]);
            }

            $subject = "Survey - {$validated['judul_survey']}";
            $description = "Permohonan Pembuatan Formulir Survei Online\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Judul Survei: {$validated['judul_survey']}\n"
                . "• Jumlah Pertanyaan: " . count($questions);

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'survey',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);

            $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

            $ticket->kegiatanDetail()->create([
                'sub_category' => 'survey',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'judul_survey' => $validated['judul_survey'],
                'pertanyaan_data' => $questions,
                'file_lampiran' => $attachmentPath,
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan Survei berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
        }

        if ($subCategory === 'post_test_pretest') {
            $validated = $request->validate([
                'tanggal_pengajuan' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'judul_acara' => ['required', 'string', 'max:255'],
                'jenis_test' => ['required', 'string', 'in:pretest,post_test'],
                'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
                'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
                'attachments' => ['nullable', 'array', 'max:5'],
                'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
                'pertanyaan_json' => ['nullable', 'string'],
            ], [
                'judul_acara.required' => 'Judul acara wajib diisi.',
                'jenis_test.required' => 'Pilih jenis evaluasi (Pretest atau Post Test).',
            ]);

            $questions = [];
            if ($request->filled('pertanyaan_json')) {
                $decoded = json_decode($request->pertanyaan_json, true);
                if (is_array($decoded)) {
                    $questions = $decoded;
                }
            }

            if (!$request->hasFile('file_lampiran') && !$request->hasFile('attachment') && !$request->hasFile('attachments') && empty($questions)) {
                return back()->withInput()->withErrors([
                    'pertanyaan_json' => 'Silakan isi pertanyaan evaluasi atau unggah dokumen soal (PDF/Word).',
                ]);
            }

            $jenisLabel = $validated['jenis_test'] === 'pretest' ? 'Pretest' : 'Post Test';
            $subject = "{$jenisLabel} - {$validated['judul_acara']}";
            $description = "Permohonan Pembuatan {$jenisLabel} Online\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Judul Acara: {$validated['judul_acara']}\n"
                . "• Jenis Evaluasi: {$jenisLabel}\n"
                . "• Jumlah Soal: " . count($questions);

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'post_test_pretest',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);

            $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

            $ticket->kegiatanDetail()->create([
                'sub_category' => 'post_test_pretest',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'judul_acara' => $validated['judul_acara'],
                'jenis_test' => $validated['jenis_test'],
                'pertanyaan_data' => $questions,
                'file_lampiran' => $attachmentPath,
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan {$jenisLabel} berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
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
        $description = "Permohonan Layanan Formulir & Kegiatan: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala: " . trim($validated['deskripsi']);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'formulir_kegiatan',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
        ]);

        $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

        $ticket->kegiatanDetail()->create([
            'sub_category' => 'lainnya',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'deskripsi' => trim($validated['deskripsi']),
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan Formulir & Kegiatan berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }
}
