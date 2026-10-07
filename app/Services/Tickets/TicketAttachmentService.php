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
                $ticket->bsreDetail?->file_lampiran
                ?: ($ticket->zoomDetail?->file_lampiran
                ?: ($ticket->kegiatanDetail?->file_lampiran
                ?: ($ticket->windowsDetail?->file_lampiran
                ?: ($ticket->officeDetail?->file_lampiran
                ?: $ticket->lainnyaDetail?->file_lampiran)))))
        };

        if (!$path) {
            abort(404, 'Berkas lampiran tidak ditemukan.');
        }

        $disk = Storage::disk('local')->exists($path)
            ? 'local'
            : (Storage::disk('public')->exists($path) ? 'public' : 'local');

        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server penyimpanan.');
        }

        if ($request->boolean('inline')) {
            return Storage::disk($disk)->response($path);
        }

        return Storage::disk($disk)->download($path);
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

        $path = $attachment->file_path;
        $disk = Storage::disk('local')->exists($path)
            ? 'local'
            : (Storage::disk('public')->exists($path) ? 'public' : 'local');

        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server penyimpanan.');
        }

        if ($request->boolean('inline')) {
            return Storage::disk($disk)->response($path, $attachment->file_name);
        }

        return Storage::disk($disk)->download($path, $attachment->file_name);
    }

    /**
     * Simpan file lampiran tunggal maupun multiple ke tabel ticket_attachments dan kembalikan path lampiran utama.
     */
    public function handleUploadedAttachments(Ticket $ticket, Request $request): ?string
    {
        $files = [];

        // 1. Ambil dari 'attachments' (multiple array atau single)
        if ($request->hasFile('attachments')) {
            $attFiles = $request->file('attachments');
            if (is_array($attFiles)) {
                $files = array_merge($files, $attFiles);
            } elseif ($attFiles instanceof \Illuminate\Http\UploadedFile) {
                $files[] = $attFiles;
            }
        }

        // 2. Ambil dari 'attachment' (single atau array)
        if ($request->hasFile('attachment')) {
            $att = $request->file('attachment');
            if (is_array($att)) {
                $files = array_merge($files, $att);
            } elseif ($att instanceof \Illuminate\Http\UploadedFile) {
                $files[] = $att;
            }
        }

        // 3. Ambil dari 'file_lampiran' (single atau array)
        if ($request->hasFile('file_lampiran')) {
            $fl = $request->file('file_lampiran');
            if (is_array($fl)) {
                $files = array_merge($files, $fl);
            } elseif ($fl instanceof \Illuminate\Http\UploadedFile) {
                $files[] = $fl;
            }
        }

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

                if (!$primaryPath) {
                    $primaryPath = $path;
                }
            }
        }

        if ($primaryPath && !$ticket->attachment) {
            $ticket->attachment = $primaryPath;
            $ticket->save();
        }

        return $primaryPath;
    }
}
