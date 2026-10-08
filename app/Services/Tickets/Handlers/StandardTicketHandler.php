<?php

namespace App\Services\Tickets\Handlers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\TicketAttachmentService;
use App\Services\Tickets\TicketDynamicPayloadCompiler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StandardTicketHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
        protected TicketDynamicPayloadCompiler $payloadCompiler,
    ) {}

    public function handle(Request $request): RedirectResponse
    {
        $serviceKey = $request->input('service_key');
        $subcategoryKey = $request->input('subcategory_key') ?: $request->input('subcategory');
        $services = config('services_gateway.services', []);
        $service = $services[$serviceKey] ?? null;

        // Compile dynamic details and auto-generate subject & description if service is selected
        [$generatedSubject, $generatedDescription] = $this->payloadCompiler->compile($request, $service, $subcategoryKey);

        if (!$request->filled('subject') && $generatedSubject) {
            $request->merge(['subject' => $generatedSubject]);
        }
        if (!$request->filled('description') && $generatedDescription) {
            $request->merge(['description' => $generatedDescription]);
        }

        // Auto-resolve category safely based on serviceKey or valid category_id
        $validCategory = $serviceKey ? Category::resolveForService($serviceKey) : null;
        if (!$validCategory) {
            $catId = $request->input('category_id');
            $validCategory = $catId ? Category::find($catId) : null;
        }
        if (!$validCategory) {
            $validCategory = Category::first();
        }
        if ($validCategory) {
            $request->merge(['category_id' => $validCategory->id]);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:5'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt,xls,xlsx,csv,webp', 'max:102400'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt,xls,xlsx,csv,webp', 'max:102400'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt,xls,xlsx,csv,webp', 'max:102400'],
        ], [
            'category_id.required' => 'Silakan pilih kategori pengaduan.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'subject.required' => 'Judul / subjek masalah wajib diisi.',
            'description.required' => 'Deskripsi kendala atau isian formulir wajib diisi.',
            'attachment.max' => 'Ukuran lampiran maksimal 100MB.',
            'attachment.mimes' => 'Format file lampiran harus berupa JPG, PNG, WEBP, PDF, DOC, DOCX, XLS, XLSX, CSV, ZIP, atau TXT.',
            'file_lampiran.max' => 'Ukuran lampiran maksimal 100MB.',
            'file_lampiran.mimes' => 'Format file lampiran harus berupa JPG, PNG, WEBP, PDF, DOC, DOCX, XLS, XLSX, CSV, ZIP, atau TXT.',
            'attachments.max' => 'Maksimal jumlah berkas yang dapat diunggah adalah 10 dokumen.',
            'attachments.*.max' => 'Ukuran masing-masing berkas maksimal 100MB.',
            'attachments.*.mimes' => 'Format berkas harus berupa JPG, PNG, WEBP, PDF, DOC, DOCX, XLS, XLSX, CSV, ZIP, atau TXT.',
        ]);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'service_key' => $serviceKey,
            'subcategory' => $subcategoryKey,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        $this->attachmentService->handleUploadedAttachments($ticket, $request);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}. Tim IT BBPOM akan segera meninjau tiket Anda.");
    }
}
