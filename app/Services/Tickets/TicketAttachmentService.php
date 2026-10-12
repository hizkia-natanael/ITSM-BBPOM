<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentService
{
    /**
     * Unduh atau pratinjau berkas lampiran legacy tiket secara aman dengan verifikasi hak akses.
     */
    public function downloadAttachment(Request $request, Ticket $ticket, ?string $type = null)
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh lampiran tiket ini.');
        }

        $path = match ($type) {
            'bsre' => $ticket->bsreDetail?->file_lampiran,
            'zoom' => $ticket->zoomDetail?->file_lampiran,
            'kegiatan' => $ticket->kegiatanDetail?->file_lampiran,
            'windows' => $ticket->windowsDetail?->file_lampiran,
            'office' => $ticket->officeDetail?->file_lampiran,
            'lainnya' => $ticket->lainnyaDetail?->file_lampiran,
            default => $ticket->attachment ?: (
                $ticket->bsreDetail?->file_lampiran ?:
                ($ticket->zoomDetail?->file_lampiran ?:
                ($ticket->kegiatanDetail?->file_lampiran ?:
                ($ticket->windowsDetail?->file_lampiran ?:
                ($ticket->officeDetail?->file_lampiran ?: $ticket->lainnyaDetail?->file_lampiran))))
            )
        };

        if (!$path) {
            abort(404, 'Berkas lampiran tidak ditemukan.');
        }

        $disk = $this->resolveDisk($path);
        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server penyimpanan.');
        }

        return $request->boolean('inline')
            ? Storage::disk($disk)->response($path)
            : Storage::disk($disk)->download($path);
    }

    /**
     * Unduh atau pratinjau salah satu item lampiran dari banyak berkas tiket secara aman.
     */
    public function downloadAttachmentItem(Request $request, Ticket $ticket, TicketAttachment $attachment)
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh lampiran tiket ini.');
        }

        if ((int) $attachment->ticket_id !== (int) $ticket->id) {
            abort(404, 'Berkas lampiran tidak cocok dengan nomor tiket.');
        }

        $disk = $this->resolveDisk($attachment->file_path);
        if (!Storage::disk($disk)->exists($attachment->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server penyimpanan.');
        }

        return $request->boolean('inline')
            ? Storage::disk($disk)->response($attachment->file_path, $attachment->file_name)
            : Storage::disk($disk)->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Simpan file lampiran tunggal maupun multiple ke tabel ticket_attachments.
     */
    public function handleUploadedAttachments(Ticket $ticket, Request $request): ?string
    {
        $files = $this->collectUploadedFiles($request);
        $primaryPath = null;
        $targetDisk = config('filesystems.attachment_disk', 'local');

        foreach ($files as $file) {
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
            $ticket->attachment = $primaryPath;
            $ticket->save();
        }

        return $primaryPath;
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
        return Storage::disk('local')->exists($path)
            ? 'local'
            : (Storage::disk('public')->exists($path) ? 'public' : 'local');
    }
}
