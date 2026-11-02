<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketResponseAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentService
{
    public function downloadAttachment(Request $request, Ticket $ticket, ?string $type = null)
    {
        $this->authorizeTicketAccess($ticket, 'Anda tidak memiliki hak akses untuk mengunduh lampiran tiket ini.');
        $path = match ($type) {
            'bsre' => $ticket->bsreDetail?->file_lampiran,
            'zoom' => $ticket->zoomDetail?->file_lampiran,
            'kegiatan' => $ticket->kegiatanDetail?->file_lampiran,
            'windows' => $ticket->windowsDetail?->file_lampiran,
            'office' => $ticket->officeDetail?->file_lampiran,
            'lainnya' => $ticket->lainnyaDetail?->file_lampiran,
            default => $ticket->attachment
                ?: $ticket->bsreDetail?->file_lampiran
                ?: $ticket->zoomDetail?->file_lampiran
                ?: $ticket->kegiatanDetail?->file_lampiran
                ?: $ticket->windowsDetail?->file_lampiran
                ?: $ticket->officeDetail?->file_lampiran
                ?: $ticket->lainnyaDetail?->file_lampiran,
        };
        if (!$path) {
            abort(404, 'Berkas lampiran tidak ditemukan.');
        }
        return $this->streamOrDownload($request, $path);
    }

    public function downloadAttachmentItem(Request $request, Ticket $ticket, TicketAttachment $attachment)
    {
        $this->authorizeTicketAccess($ticket, 'Anda tidak memiliki hak akses untuk mengunduh lampiran tiket ini.');
        if ((int) $attachment->ticket_id !== (int) $ticket->id) {
            abort(404, 'Berkas lampiran tidak cocok dengan nomor tiket.');
        }
        return $this->streamOrDownload($request, $attachment->file_path, $attachment->file_name);
    }

    public function downloadResponseAttachment(Request $request, Ticket $ticket, TicketResponseAttachment $attachment)
    {
        $this->authorizeTicketAccess($ticket, 'Anda tidak memiliki hak akses untuk mengunduh berkas tanggapan tiket ini.');
        if ((int) $attachment->ticketResponse?->ticket_id !== (int) $ticket->id) {
            abort(404, 'Berkas lampiran tidak cocok dengan nomor tiket.');
        }
        return $this->streamOrDownload($request, $attachment->file_path, $attachment->file_name, $attachment->mime_type);
    }

    public function handleUploadedAttachments(Ticket $ticket, Request $request): ?string
    {
        $primaryPath = null;
        $targetDisk = config('filesystems.attachment_disk', 'local');
        foreach ($this->collectUploadedFiles($request) as $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('attachments', $targetDisk);
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
                $primaryPath ??= $path;
            }
        }
        if ($primaryPath && !$ticket->attachment) {
            $ticket->update(['attachment' => $primaryPath]);
        }
        return $primaryPath;
    }

    private function authorizeTicketAccess(Ticket $ticket, string $message): void
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, $message);
        }
    }

    private function streamOrDownload(Request $request, string $path, ?string $name = null, ?string $mime = null)
    {
        $disk = $this->resolveDisk($path);
        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server penyimpanan.');
        }
        if ($request->boolean('inline')) {
            $headers = $mime ? ['Content-Type' => $mime] : [];
            return Storage::disk($disk)->response($path, $name, $headers);
        }

        return Storage::disk($disk)->download($path, $name);
    }

    private function collectUploadedFiles(Request $request): array
    {
        $files = [];
        foreach (['attachments', 'attachment', 'file_lampiran'] as $key) {
            if ($request->hasFile($key)) {
                $item = $request->file($key);
                $files = array_merge($files, is_array($item) ? $item : [$item]);
            }
        }
        return $files;
    }

    private function resolveDisk(string $path): string
    {
        $configured = config('filesystems.attachment_disk', 'local');
        if (Storage::disk($configured)->exists($path)) {
            return $configured;
        }
        return Storage::disk('local')->exists($path) ? 'local' : (Storage::disk('public')->exists($path) ? 'public' : 'local');
    }
}
