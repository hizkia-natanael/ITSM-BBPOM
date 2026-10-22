<?php

namespace App\Services\Tickets\Handlers\Kegiatan;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\TicketAttachmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KegiatanEvaluationHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
    ) {}

    public function handleSurvey(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'judul_survey' => ['required', 'string', 'max:255'],
            'pertanyaan_json' => ['nullable', 'string'],
        ], $this->fileValidationRules()), ['judul_survey.required' => 'Judul survei wajib diisi.']);
        $questions = $this->decodeQuestions($request);
        if (!$this->hasUploadedFile($request) && empty($questions)) {
            return back()->withInput()->withErrors([
                'pertanyaan_json' => 'Silakan isi minimal 1 pertanyaan survei atau unggah dokumen draf pertanyaan (PDF/Word).',
            ]);
        }
        $description = "Permohonan Pembuatan Formulir Survei Online\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n• Judul Survei: {$validated['judul_survey']}\n• Jumlah Pertanyaan: " . count($questions);
        $ticket = $this->createTicket($request, $category, 'survey', "Survey - {$validated['judul_survey']}", $description);
        $ticket->kegiatanDetail()->create([
            'sub_category' => 'survey',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'judul_survey' => $validated['judul_survey'],
            'pertanyaan_data' => $questions,
            'file_lampiran' => $this->attachmentService->handleUploadedAttachments($ticket, $request),
        ]);
        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Permohonan Survei berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    public function handleTest(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'judul_acara' => ['required', 'string', 'max:255'],
            'jenis_test' => ['required', 'string', 'in:pretest,post_test'],
            'pertanyaan_json' => ['nullable', 'string'],
        ], $this->fileValidationRules()), [
            'judul_acara.required' => 'Judul acara wajib diisi.',
            'jenis_test.required' => 'Pilih jenis evaluasi (Pretest atau Post Test).',
        ]);
        $questions = $this->decodeQuestions($request);
        if (!$this->hasUploadedFile($request) && empty($questions)) {
            return back()->withInput()->withErrors([
                'pertanyaan_json' => 'Silakan isi pertanyaan evaluasi atau unggah dokumen soal (PDF/Word).',
            ]);
        }
        $jenisLabel = $validated['jenis_test'] === 'pretest' ? 'Pretest' : 'Post Test';
        $description = "Permohonan Pembuatan {$jenisLabel} Online\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n• Judul Acara: {$validated['judul_acara']}\n"
            . "• Jenis Evaluasi: {$jenisLabel}\n• Jumlah Soal: " . count($questions);
        $ticket = $this->createTicket($request, $category, 'post_test_pretest', "{$jenisLabel} - {$validated['judul_acara']}", $description);
        $ticket->kegiatanDetail()->create([
            'sub_category' => 'post_test_pretest',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'judul_acara' => $validated['judul_acara'],
            'jenis_test' => $validated['jenis_test'],
            'pertanyaan_data' => $questions,
            'file_lampiran' => $this->attachmentService->handleUploadedAttachments($ticket, $request),
        ]);
        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Permohonan {$jenisLabel} berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
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

    private function fileValidationRules(): array
    {
        return [
            'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip', 'max:5120'],
        ];
    }

    private function decodeQuestions(Request $request): array
    {
        $decoded = $request->filled('pertanyaan_json') ? json_decode($request->pertanyaan_json, true) : null;
        return is_array($decoded) ? $decoded : [];
    }

    private function hasUploadedFile(Request $request): bool
    {
        return $request->hasFile('file_lampiran') || $request->hasFile('attachment') || $request->hasFile('attachments');
    }
}
