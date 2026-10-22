<?php

namespace App\Services\Tickets\Handlers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\Tickets\TicketAttachmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BsreTicketHandler
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
    ) {}

    public function handle(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'reset_password');
        $category = Category::resolveForService('sertifikat_bse');
        if (in_array($subCategory, ['reset_password', 'permintaan_otp'])) {
            $validated = $request->validate([
                'tanggal_pengajuan' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'nip' => ['required', 'string', 'max:50'],
                'nik' => ['required', 'string', 'max:30'],
                'email' => ['required', 'email', 'max:255'],
                'nomor_telepon' => ['required', 'string', 'max:30'],
            ], [
                'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi.',
                'nama.required' => 'Nama lengkap pemohon wajib diisi.',
                'nip.required' => 'NIP wajib diisi.',
                'nik.required' => 'NIK wajib diisi.',
                'email.required' => 'Alamat email aktif wajib diisi.',
                'nomor_telepon.required' => 'Nomor telepon / WhatsApp wajib diisi.',
            ]);
            $subLabel = $subCategory === 'reset_password' ? 'Reset Password BSRE' : 'Permintaan OTP';
            $subject = "{$subLabel} - {$validated['nama']}";
            $description = "Permohonan Layanan Sertifikat BSrE: {$subLabel}\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Nama Pemohon: {$validated['nama']}\n"
                . "• NIP: {$validated['nip']}\n"
                . "• NIK: {$validated['nik']}\n"
                . "• Email: {$validated['email']}\n"
                . "• Nomor Telepon/WA: {$validated['nomor_telepon']}";
            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'sertifikat_bse',
                'subcategory' => $subCategory,
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);
            $ticket->bsreDetail()->create([
                'sub_category' => $subCategory,
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'nip' => $validated['nip'],
                'nik' => $validated['nik'],
                'email' => $validated['email'],
                'nomor_telepon' => $validated['nomor_telepon'],
            ]);

            // Auto-update user profile with NIP, NIK, Phone if previously empty
            $user = $request->user();
            $profileUpdates = [];
            if (empty($user->nip) && !empty($validated['nip'])) $profileUpdates['nip'] = $validated['nip'];
            if (empty($user->nik) && !empty($validated['nik'])) $profileUpdates['nik'] = $validated['nik'];
            if (empty($user->phone_number) && !empty($validated['nomor_telepon'])) $profileUpdates['phone_number'] = $validated['nomor_telepon'];
            if (!empty($profileUpdates)) $user->update($profileUpdates);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan {$subLabel} berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
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
        $description = "Permohonan Layanan Sertifikat BSrE: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Nama Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala: " . trim($validated['deskripsi']);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'sertifikat_bse',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
        ]);

        $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

        $ticket->bsreDetail()->create([
            'sub_category' => 'lainnya',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'deskripsi' => trim($validated['deskripsi']),
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan BSRE berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }
}
