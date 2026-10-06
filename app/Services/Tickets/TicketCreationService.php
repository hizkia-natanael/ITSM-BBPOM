<?php

namespace App\Services\Tickets;

use App\Models\Category;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketCreationService
{
    public function __construct(
        protected TicketAttachmentService $attachmentService,
        protected TicketDynamicPayloadCompiler $payloadCompiler,
    ) {}

    /**
     * Dispatch penyimpanan tiket berdasarkan service_key.
     */
    public function store(Request $request): RedirectResponse
    {
        $serviceKey = $request->input('service_key');

        return match ($serviceKey) {
            'sertifikat_bse' => $this->storeBsreTicket($request),
            'zoom' => $this->storeZoomTicket($request),
            'formulir_kegiatan' => $this->storeKegiatanTicket($request),
            'install_windows' => $this->storeWindowsTicket($request),
            'lisensi_office' => $this->storeOfficeTicket($request),
            'lainnya' => $this->storeLainnyaTicket($request),
            default => $this->storeStandardTicket($request),
        };
    }

    /**
     * 1. Handler Layanan Sertifikat BSrE
     */
    public function storeBsreTicket(Request $request): RedirectResponse
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
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

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
            'deskripsi' => $validated['deskripsi'],
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan BSRE berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    /**
     * 2. Handler Layanan Zoom
     */
    public function storeZoomTicket(Request $request): RedirectResponse
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
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
        ]);

        $subject = "Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Zoom: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Nama Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

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
            'deskripsi' => $validated['deskripsi'],
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan Zoom berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    /**
     * 3. Handler Layanan Formulir & Kegiatan
     */
    public function storeKegiatanTicket(Request $request): RedirectResponse
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
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

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
            'deskripsi' => $validated['deskripsi'],
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan Formulir & Kegiatan berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    /**
     * 4. Handler Layanan Install Ulang Windows
     */
    public function storeWindowsTicket(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_install_windows');
        $category = Category::resolveForService('install_windows');

        if ($subCategory === 'permintaan_install_windows') {
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
                'deskripsi.required' => 'Deskripsi kendala / alasan install ulang wajib diisi.',
            ]);

            $subject = "Install Windows ({$validated['merk_perangkat']}) - {$validated['nama']}";
            $description = "Permohonan Install Ulang Operating System Windows\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Bidang / Substansi: {$validated['bidang']}\n"
                . "• Merk Perangkat: {$validated['merk_perangkat']}\n"
                . "• Kode BMN: {$validated['kode_bmn']}\n\n"
                . "Alasan / Deskripsi Kendala:\n" . $validated['deskripsi'];

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $category->id,
                'service_key' => 'install_windows',
                'subcategory' => 'permintaan_install_windows',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
            ]);

            $ticket->windowsDetail()->create([
                'sub_category' => 'permintaan_install_windows',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'nama' => $validated['nama'],
                'bidang' => $validated['bidang'],
                'merk_perangkat' => $validated['merk_perangkat'],
                'kode_bmn' => $validated['kode_bmn'],
                'deskripsi' => $validated['deskripsi'],
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', "Permohonan Install Ulang Windows berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
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
        $description = "Permohonan Layanan Sistem Operasi: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'install_windows',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
        ]);

        $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

        $ticket->windowsDetail()->create([
            'sub_category' => 'lainnya',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan layanan Windows berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    /**
     * 5. Handler Layanan Lisensi Microsoft Office
     */
    public function storeOfficeTicket(Request $request): RedirectResponse
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

    /**
     * 6. Handler Layanan Umum Lainnya
     */
    public function storeLainnyaTicket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'min:5'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
        ], [
            'deskripsi.required' => 'Deskripsi permintaan / kendala wajib diisi minimal 5 karakter.',
        ]);

        $subject = "Permintaan Dukungan TIK - {$validated['nama']}";
        $description = "Permohonan Dukungan Layanan TIK Umum\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Permintaan:\n" . $validated['deskripsi'];

        $category = Category::resolveForService('lainnya');

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'lainnya',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
        ]);

        $attachmentPath = $this->attachmentService->handleUploadedAttachments($ticket, $request);

        $ticket->lainnyaDetail()->create([
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'file_lampiran' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    /**
     * Fallback standard ticket submission
     */
    public function storeStandardTicket(Request $request): RedirectResponse
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
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
        ], [
            'category_id.required' => 'Silakan pilih kategori pengaduan.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'subject.required' => 'Judul / subjek masalah wajib diisi.',
            'description.required' => 'Deskripsi kendala atau isian formulir wajib diisi.',
            'attachment.max' => 'Ukuran lampiran maksimal 5MB.',
            'attachment.mimes' => 'Format file lampiran harus berupa JPG, PNG, PDF, DOC, DOCX, ZIP, atau TXT.',
            'file_lampiran.max' => 'Ukuran lampiran maksimal 5MB.',
            'file_lampiran.mimes' => 'Format file lampiran harus berupa JPG, PNG, PDF, DOC, DOCX, ZIP, atau TXT.',
            'attachments.max' => 'Maksimal jumlah berkas yang dapat diunggah adalah 5 file.',
            'attachments.*.max' => 'Ukuran masing-masing berkas maksimal 5MB.',
            'attachments.*.mimes' => 'Format berkas harus berupa JPG, PNG, PDF, DOC, DOCX, ZIP, atau TXT.',
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

    /**
     * Resolusi ID Kategori secara aman.
     */
    public function resolveCategoryId(Request $request, string $defaultSlug = 'aplikasi-software', ?string $serviceKey = null): int
    {
        $catId = $request->input('category_id');
        if ($catId && Category::where('id', $catId)->exists()) {
            return (int) $catId;
        }

        if ($serviceKey) {
            $cat = Category::resolveForService($serviceKey);
            if ($cat) {
                return (int) $cat->id;
            }
        }

        $cat = Category::where('slug', $defaultSlug)->first()
            ?? Category::where('slug', 'aplikasi-software')->first()
            ?? Category::first();

        return $cat ? (int) $cat->id : 1;
    }
}
