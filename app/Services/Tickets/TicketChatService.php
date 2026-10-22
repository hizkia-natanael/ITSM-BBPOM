<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Services\FileCompressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketChatService
{
    public function __construct(
        protected TicketResponseSerializer $serializer,
    ) {}

    public function reply(Request $request, Ticket $ticket): RedirectResponse|JsonResponse
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }
        $hasAnyFile = $request->hasFile('attachment') || $request->hasFile('attachments');
        $validated = $request->validate([
            'response_text' => [$hasAnyFile ? 'nullable' : 'required', 'string'],
            'reply_to_id' => ['nullable', 'integer', 'exists:ticket_responses,id'],
            'attachment' => ['nullable', 'file', 'max:51200'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:51200'],
            'compression_mode' => ['nullable', 'string', 'in:compressed,original'],
        ], [
            'response_text.required' => 'Pesan tanggapan tidak boleh kosong jika tidak melampirkan berkas.',
            'attachment.max' => 'Ukuran berkas lampiran tidak boleh melebihi 50 MB.',
            'attachments.*.max' => 'Ukuran berkas lampiran tidak boleh melebihi 50 MB.',
        ]);
        $uploadedFiles = $this->extractUploadedFiles($request);
        $responseText = trim((string) ($validated['response_text'] ?? ''));
        if ($responseText === '' && !empty($uploadedFiles)) {
            $responseText = count($uploadedFiles) > 1
                ? 'Melampirkan ' . count($uploadedFiles) . ' berkas (Arsip ZIP).'
                : 'Melampirkan berkas: ' . $uploadedFiles[0]->getClientOriginalName();
        }
        $replyToId = !empty($validated['reply_to_id'])
            ? $ticket->responses()->where('id', $validated['reply_to_id'])->value('id')
            : null;
        $response = $ticket->responses()->create([
            'user_id' => $request->user()->id,
            'reply_to_id' => $replyToId,
            'response_text' => $responseText,
        ]);
        if (!empty($uploadedFiles)) {
            $mode = in_array($request->input('compression_mode'), ['compressed', 'original'])
                ? $request->input('compression_mode')
                : 'compressed';
            $uploadData = app(FileCompressionService::class)->processMultipleUploads($uploadedFiles, $ticket->id, $mode);
            $response->attachments()->create($uploadData);
        }
        if ($request->user()->isAdmin()) {
            $ticket->handled_by_id = $ticket->handled_by_id ?: $request->user()->id;
            $ticket->status = $ticket->status === 'pending' ? 'diproses' : $ticket->status;
            $ticket->save();
        }
        $response->loadMissing(['user', 'attachments', 'replyTo.user', 'replyTo.attachments']);
        $msg = $request->user()->isAdmin()
            ? 'Tanggapan dan berkas Anda telah berhasil dikirim ke pelapor.'
            : 'Tanggapan dan berkas Anda telah berhasil dikirim ke tim IT BBPOM.';
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'response' => $this->serializer->format($response, (int) $request->user()->id),
            ]);
        }
        if ($request->user()->isAdmin() && (str_contains(url()->previous(), '/admin') || str_contains((string) $request->header('referer'), '/admin'))) {
            return redirect()->back()->with('success', $msg);
        }
        return redirect()->route('tickets.show', $ticket)->with('success', $msg);
    }

    public function chatSync(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }
        $afterId = (int) $request->query('after_id', 0);
        $currentUserId = (int) auth()->id();
        $query = $ticket->responses()
            ->with(['user', 'attachments', 'replyTo.user', 'replyTo.attachments'])
            ->orderBy('id', 'asc');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $responses = $query->get();
        if ($responses->isNotEmpty()) {
            $ticket->responses()->where('user_id', '!=', $currentUserId)->whereNull('read_at')->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'ticket_status' => $ticket->status,
            'handled_by' => $ticket->handledBy ? ['id' => $ticket->handledBy->id, 'name' => $ticket->handledBy->name] : null,
            'has_admin_response' => $ticket->responses()->whereHas('user', fn ($q) => $q->where('role', 'admin'))->exists(),
            'updated_at_formatted' => $ticket->updated_at->translatedFormat('d M Y, H:i') . ' WIB',
            'responses' => $responses->map(fn (TicketResponse $res) => $this->serializer->format($res, $currentUserId)),
            'latest_id' => $responses->isNotEmpty() ? $responses->last()->id : $afterId,
        ]);
    }

    private function extractUploadedFiles(Request $request): array
    {
        if ($request->hasFile('attachments') && is_array($request->file('attachments'))) {
            return array_values(array_filter($request->file('attachments')));
        }
        return $request->hasFile('attachment') ? [$request->file('attachment')] : [];
    }
}
