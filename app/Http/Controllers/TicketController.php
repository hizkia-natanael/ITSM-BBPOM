<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * User interactive landing/dashboard.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'total' => $user->tickets()->count(),
            'pending' => $user->tickets()->where('status', 'pending')->count(),
            'diproses' => $user->tickets()->where('status', 'diproses')->count(),
            'selesai' => $user->tickets()->where('status', 'selesai')->count(),
            'ditolak' => $user->tickets()->where('status', 'ditolak')->count(),
        ];

        $recentTickets = $user->tickets()
            ->with(['category', 'responses'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
        ]);
    }

    /**
     * History / Daftar Pengaduan milik user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = $user->tickets()->with('category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('tickets.index', [
            'tickets' => $tickets,
            'categories' => $categories,
            'selectedStatus' => $request->status,
            'selectedCategory' => $request->category_id,
            'search' => $request->search,
        ]);
    }

    /**
     * Form Tambah Pengaduan (App Gateway & Dynamic Form).
     */
    public function create(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $services = config('services_gateway.services', []);
        $groups = config('services_gateway.groups', []);
        $initialService = $request->query('service');
        $initialSubcategory = $request->query('subcategory');
        $user = $request->user();

        return view('tickets.create', [
            'categories' => $categories,
            'services' => $services,
            'groups' => $groups,
            'initialService' => $initialService,
            'initialSubcategory' => $initialSubcategory,
            'user' => $user,
            'todayDate' => Carbon::now()->format('Y-m-d'),
            'todayFormatted' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }

    /**
     * Simpan Pengaduan baru (Mendukung formulir dinamis layanan 1, 2, 3, 7, 8, 9).
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
    protected function storeBsreTicket(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'reset_password');

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
            $subject = "[Sertifikat BSrE] {$subLabel} - {$validated['nama']}";

            $description = "Permohonan Layanan Sertifikat BSrE: {$subLabel}\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Nama Pemohon: {$validated['nama']}\n"
                . "• NIP: {$validated['nip']}\n"
                . "• NIK: {$validated['nik']}\n"
                . "• Email: {$validated['email']}\n"
                . "• Nomor Telepon/WA: {$validated['nomor_telepon']}";

            $category = Category::where('slug', 'sertifikat-bsre')->first() ?? Category::first();

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
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
        ]);

        $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
        $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

        $subject = "[Sertifikat BSrE] Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Sertifikat BSrE: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Nama Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

        $category = Category::where('slug', 'sertifikat-bsre')->first() ?? Category::first();

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
            'service_key' => 'sertifikat_bse',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

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
    protected function storeZoomTicket(Request $request): RedirectResponse
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

            $subject = "[Zoom] Permintaan Link Zoom - {$validated['nama_acara']}";
            $description = "Permohonan Layanan Akun Zoom Meeting / Webinar\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Nama Pemohon: {$validated['nama']}\n"
                . "• Nama Acara: {$validated['nama_acara']}\n"
                . "• Jam / Waktu Pelaksanaan: {$validated['jam']}";

            $category = Category::where('slug', 'zoom')->first() ?? Category::first();

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
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
        ]);

        $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
        $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

        $subject = "[Zoom] Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Zoom: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Nama Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

        $category = Category::where('slug', 'zoom')->first() ?? Category::first();

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
            'service_key' => 'zoom',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

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
     * 3. Handler Layanan Formulir & Kegiatan (Daftar Hadir, QR Code, Survey, Pretest/Posttest, Lainnya)
     */
    protected function storeKegiatanTicket(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_daftar_hadir');
        $category = Category::where('slug', 'formulir-kegiatan')->first() ?? Category::first();

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

            $subject = "[Formulir & Kegiatan] Permintaan Daftar Hadir - {$validated['nama_acara']}";
            $description = "Permohonan Pembuatan Presensi / Daftar Hadir Online\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Nama Acara: {$validated['nama_acara']}\n"
                . "• Jam / Waktu Pelaksanaan: {$validated['jam']}";

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
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

            $subject = "[Formulir & Kegiatan] Permintaan QR Code - {$validated['nama']}";
            $description = "Permohonan Generate QR Code Resmi\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Tautan Sasaran: {$validated['link_qrcode']}";

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
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
            if (!$request->hasFile('file_lampiran') && empty($questions)) {
                return back()->withInput()->withErrors([
                    'pertanyaan_json' => 'Silakan isi minimal 1 pertanyaan survei atau unggah dokumen draf pertanyaan (PDF/Word).',
                ]);
            }

            $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
            $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

            $subject = "[Formulir & Kegiatan] Survey - {$validated['judul_survey']}";
            $description = "Permohonan Pembuatan Formulir Survei Online\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Judul Survei: {$validated['judul_survey']}\n"
                . "• Jumlah Pertanyaan: " . count($questions) . ($attachmentPath ? " (Disertai Dokumen Lampiran)" : "");

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'survey',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
                'attachment' => $attachmentPath,
            ]);

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

            if (!$request->hasFile('file_lampiran') && !$request->hasFile('attachment') && empty($questions)) {
                return back()->withInput()->withErrors([
                    'pertanyaan_json' => 'Silakan isi pertanyaan evaluasi atau unggah dokumen soal (PDF/Word).',
                ]);
            }

            $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
            $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

            $jenisLabel = $validated['jenis_test'] === 'pretest' ? 'Pretest' : 'Post Test';
            $subject = "[Formulir & Kegiatan] {$jenisLabel} - {$validated['judul_acara']}";
            $description = "Permohonan Pembuatan {$jenisLabel} Online\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Judul Acara: {$validated['judul_acara']}\n"
                . "• Jenis Evaluasi: {$jenisLabel}\n"
                . "• Jumlah Soal: " . count($questions) . ($attachmentPath ? " (Disertai Dokumen Soal Lampiran)" : "");

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
                'service_key' => 'formulir_kegiatan',
                'subcategory' => 'post_test_pretest',
                'subject' => $subject,
                'description' => $description,
                'status' => 'pending',
                'attachment' => $attachmentPath,
            ]);

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
        ]);

        $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
        $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

        $subject = "[Formulir & Kegiatan] Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Formulir & Kegiatan: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
            'service_key' => 'formulir_kegiatan',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

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
    protected function storeWindowsTicket(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_install_windows');
        $category = Category::where('slug', 'sistem-operasi')->first() ?? Category::first();

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

            $subject = "[Install Windows] {$validated['merk_perangkat']} ({$validated['bidang']}) - {$validated['nama']}";
            $description = "Permohonan Install Ulang Operating System Windows\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Bidang / Substansi: {$validated['bidang']}\n"
                . "• Merk Perangkat: {$validated['merk_perangkat']}\n"
                . "• Kode BMN: {$validated['kode_bmn']}\n\n"
                . "Alasan / Deskripsi Kendala:\n" . $validated['deskripsi'];

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
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
        ]);

        $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
        $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

        $subject = "[Install Windows] Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Sistem Operasi: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
            'service_key' => 'install_windows',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

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
    protected function storeOfficeTicket(Request $request): RedirectResponse
    {
        $subCategory = $request->input('subcategory', 'permintaan_lisensi_office');
        $category = Category::where('slug', 'aplikasi-perkantoran')->first() ?? Category::first();

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

            $subject = "[Lisensi Office] {$validated['merk_perangkat']} ({$validated['bidang']}) - {$validated['nama']}";
            $description = "Permohonan Lisensi Resmi Microsoft Office\n\n"
                . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
                . "• Pemohon: {$validated['nama']}\n"
                . "• Bidang / Substansi: {$validated['bidang']}\n"
                . "• Merk Perangkat: {$validated['merk_perangkat']}\n"
                . "• Kode BMN: {$validated['kode_bmn']}\n\n"
                . "Deskripsi Kendala:\n" . $validated['deskripsi'];

            $ticket = Ticket::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
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
        ]);

        $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
        $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

        $subject = "[Lisensi Office] Layanan Lainnya - {$validated['nama']}";
        $description = "Permohonan Layanan Lisensi Office: Lainnya\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Kebutuhan/Kendala:\n" . $validated['deskripsi'];

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
            'service_key' => 'lisensi_office',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

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
    protected function storeLainnyaTicket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'min:5'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
        ], [
            'deskripsi.required' => 'Deskripsi permintaan / kendala wajib diisi minimal 5 karakter.',
        ]);

        $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
        $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

        $subject = "[Lainnya] Permintaan Dukungan TIK - {$validated['nama']}";
        $description = "Permohonan Dukungan Layanan TIK Umum\n\n"
            . "• Tanggal Pengajuan: " . Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y') . "\n"
            . "• Pemohon: {$validated['nama']}\n\n"
            . "Deskripsi Permintaan:\n" . $validated['deskripsi'];

        $category = Category::where('slug', 'lainnya')->first() ?? Category::first();

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->input('category_id') ?: ($category ? $category->id : 1),
            'service_key' => 'lainnya',
            'subcategory' => 'lainnya',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

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
    protected function storeStandardTicket(Request $request): RedirectResponse
    {
        $serviceKey = $request->input('service_key');
        $subcategoryKey = $request->input('subcategory_key') ?: $request->input('subcategory');
        $services = config('services_gateway.services', []);
        $service = $services[$serviceKey] ?? null;

        // Compile dynamic details and auto-generate subject & description if service is selected
        [$generatedSubject, $generatedDescription] = $this->compileDynamicPayload($request, $service, $subcategoryKey);

        if (!$request->filled('subject') && $generatedSubject) {
            $request->merge(['subject' => $generatedSubject]);
        }
        if (!$request->filled('description') && $generatedDescription) {
            $request->merge(['description' => $generatedDescription]);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:5'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
            'file_lampiran' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,zip,txt', 'max:5120'],
        ], [
            'category_id.required' => 'Silakan pilih kategori pengaduan.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'subject.required' => 'Judul / subjek masalah wajib diisi.',
            'description.required' => 'Deskripsi kendala atau isian formulir wajib diisi.',
            'attachment.max' => 'Ukuran lampiran maksimal 5MB.',
            'attachment.mimes' => 'Format file lampiran harus berupa JPG, PNG, PDF, DOC, DOCX, ZIP, atau TXT.',
            'file_lampiran.max' => 'Ukuran lampiran maksimal 5MB.',
            'file_lampiran.mimes' => 'Format file lampiran harus berupa JPG, PNG, PDF, DOC, DOCX, ZIP, atau TXT.',
        ]);

        $uploadedFile = $request->file('file_lampiran') ?: $request->file('attachment');
        $attachmentPath = $uploadedFile ? $uploadedFile->store('attachments', 'public') : null;

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'service_key' => $serviceKey,
            'subcategory' => $subcategoryKey,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Pengaduan berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}. Tim IT BBPOM akan segera meninjau tiket Anda.");
    }

    /**
     * Kompilasi data dinamis dari formulir spesifikasi layanan TIK.
     */
    protected function compileDynamicPayload(Request $request, ?array $service, ?string $subKey): array
    {
        if (!$service) {
            return [$request->input('subject'), $request->input('description')];
        }

        $serviceName = $service['name'] ?? 'Layanan TI';
        $subcategories = $service['subcategories'] ?? [];
        $subLabel = $subcategories[$subKey] ?? ($request->input('custom_subcategory') ?: 'Umum');

        $lines = [];
        $lines[] = "**Layanan:** {$serviceName}";
        $lines[] = "**Sub Kategori:** {$subLabel}";
        $lines[] = "**Tanggal Pengajuan:** " . now()->format('d/m/Y H:i');
        $lines[] = "**Nama Pemohon:** " . ($request->user() ? $request->user()->name : '-');
        $lines[] = "---";
        $lines[] = "### Rincian Kebutuhan / Formulir:";

        $subjectSuffix = '';

        // 1. SIPT
        if ($service['id'] === 'sipt') {
            if ($subKey === 'reset_password') {
                $userSipt = $request->input('sipt_username', '-');
                $lines[] = "* **Username SIPT:** {$userSipt}";
                if ($reqPass = $request->input('sipt_password')) {
                    $lines[] = "* **Request Password Baru:** {$reqPass}";
                }
                $subjectSuffix = "Reset Password ({$userSipt})";
            } elseif ($subKey === 'rollback_sampel') {
                $noSampel = $request->input('sipt_sample_number', '-');
                $komoditi = $request->input('sipt_commodity', '-');
                $lines[] = "* **Nomor Sampel:** {$noSampel}";
                $lines[] = "* **Komoditi:** {$komoditi}";
                $subjectSuffix = "Rollback Sampel {$noSampel} ({$komoditi})";
            } elseif ($subKey === 'perbaikan_sampel') {
                $kdSampel = $request->input('sipt_sample_code', '-');
                $komoditi = $request->input('sipt_commodity', '-');
                $nmSampel = $request->input('sipt_sample_name', '-');
                $lines[] = "* **Kode Sampel:** {$kdSampel}";
                $lines[] = "* **Komoditi:** {$komoditi}";
                $lines[] = "* **Nama Sampel:** {$nmSampel}";
                $lines[] = "* **Sebelum Perbaikan:** " . $request->input('sipt_before_repair', '-');
                $lines[] = "* **Perbaikan:** " . $request->input('sipt_after_repair', '-');
                $subjectSuffix = "Perbaikan Sampel {$kdSampel} ({$nmSampel})";
            } elseif ($subKey === 'cek_posisi_sampel') {
                $kdSampel = $request->input('sipt_sample_code', '-');
                $komoditi = $request->input('sipt_commodity', '-');
                $nmSampel = $request->input('sipt_sample_name', '-');
                $lines[] = "* **Kode Sampel:** {$kdSampel}";
                $lines[] = "* **Komoditi:** {$komoditi}";
                $lines[] = "* **Nama Sampel:** {$nmSampel}";
                $subjectSuffix = "Cek Posisi Sampel {$kdSampel}";
            } else {
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
                $subjectSuffix = "Kendala SIPT";
            }
        }

        // 2. Srikandi
        elseif ($service['id'] === 'srikandi') {
            if ($subKey === 'reset_password') {
                $userSrikandi = $request->input('srikandi_username', '-');
                $lines[] = "* **Username Srikandi:** {$userSrikandi}";
                if ($reqPass = $request->input('srikandi_password')) {
                    $lines[] = "* **Request Password Baru:** {$reqPass}";
                }
                $subjectSuffix = "Reset Password ({$userSrikandi})";
            } elseif ($subKey === 'buat_akun') {
                $nama = $request->input('srikandi_fullname', '-');
                $lines[] = "* **Nama Lengkap:** {$nama}";
                $lines[] = "* **NIK:** " . $request->input('srikandi_nik', '-');
                $lines[] = "* **No. Telp / WhatsApp:** " . $request->input('srikandi_phone', '-');
                $lines[] = "* **Alamat Email:** " . $request->input('srikandi_email', '-');
                $lines[] = "* **Request Username:** " . $request->input('srikandi_req_username', '-');
                if ($reqPass = $request->input('srikandi_req_password')) {
                    $lines[] = "* **Request Password Awal:** {$reqPass}";
                }
                $subjectSuffix = "Pembuatan Akun Baru ({$nama})";
            } elseif ($subKey === 'nonaktif_akun') {
                $userSrikandi = $request->input('srikandi_username', '-');
                $lines[] = "* **Username Srikandi:** {$userSrikandi}";
                $lines[] = "* **Alasan Dinonaktifkan:** " . $request->input('srikandi_disable_reason', '-');
                $subjectSuffix = "Penonaktifan Akun ({$userSrikandi})";
            } elseif ($subKey === 'reset_mfa') {
                $userSrikandi = $request->input('srikandi_username', '-');
                $lines[] = "* **Username Srikandi:** {$userSrikandi}";
                $lines[] = "* **Permintaan:** Reset MFA (Multi-Factor Authentication)";
                $subjectSuffix = "Reset MFA ({$userSrikandi})";
            } else {
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
                $subjectSuffix = "Kendala Srikandi";
            }
        }

        // 3. Email Corporate
        elseif ($service['id'] === 'email_corporate') {
            if ($subKey === 'reset_password') {
                $nip = $request->input('email_nip', '-');
                $lines[] = "* **NIP:** {$nip}";
                if ($reqPass = $request->input('email_password')) {
                    $lines[] = "* **Request Password Baru:** {$reqPass}";
                }
                $subjectSuffix = "Reset Password (NIP: {$nip})";
            } else {
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
                $subjectSuffix = "Kendala Email Corporate";
            }
        }

        // 7. Internet & Jaringan
        elseif ($service['id'] === 'internet') {
            if ($subKey === 'lemot_mati') {
                $loc = $request->input('ap_location', '-');
                $lines[] = "* **Lokasi Titik Access Point / Ruangan:** {$loc}";
                $lines[] = "* **Keluhan:** " . $request->input('complaint_details', '-');
                $subjectSuffix = "Keluhan Internet Lemot / Mati ({$loc})";
            } elseif ($subKey === 'tambah_ap') {
                $loc = $request->input('ap_location', '-');
                $lines[] = "* **Request Lokasi Access Point:** {$loc}";
                $lines[] = "* **Keluhan / Alasan Kebutuhan:** " . $request->input('complaint_details', '-');
                $subjectSuffix = "Permintaan Titik AP Baru ({$loc})";
            } else {
                $lines[] = "* **Deskripsi:** " . $request->input('service_description', $request->input('description', '-'));
                $subjectSuffix = "Kendala Jaringan & Internet";
            }
        }

        // 8. Subsite
        elseif ($service['id'] === 'subsite') {
            if ($subKey === 'upload_berita') {
                $judul = $request->input('news_title', '-');
                $lines[] = "* **Judul Berita:** {$judul}";
                $lines[] = "* **Isi Berita:**\n" . $request->input('news_content', '-');
                if ($tgl = $request->input('target_date')) {
                    $lines[] = "* **Request Tanggal Upload:** {$tgl}";
                }
                $subjectSuffix = "Upload Berita: {$judul}";
            } elseif ($subKey === 'upload_dokumen') {
                $doc = $request->input('doc_name', '-');
                $loc = $request->input('doc_location', '-');
                $lines[] = "* **Nama Dokumen:** {$doc}";
                $lines[] = "* **Lokasi Upload / Kategori:** {$loc}";
                $subjectSuffix = "Upload Dokumen: {$doc}";
            } elseif ($subKey === 'hapus_dokumen') {
                $doc = $request->input('doc_name', '-');
                $lines[] = "* **Judul Dokumen yang Akan Dihapus:** {$doc}";
                $lines[] = "* **Link Dokumen:** " . $request->input('doc_url', '-');
                $subjectSuffix = "Hapus Dokumen: {$doc}";
            }
        }

        // 9. Infografis
        elseif ($service['id'] === 'infografis') {
            $judul = $request->input('infographic_title', '-');
            $lines[] = "* **Judul Infografis:** {$judul}";
            $lines[] = "* **Isi Materi / Pesan Utama:**\n" . $request->input('infographic_content', '-');
            $subjectSuffix = "Pembuatan Infografis: {$judul}";
        }

        // Fallback / Other services (4, 5, 6, 10, 11, 12)
        else {
            $desc = $request->input('description', $request->input('service_description', '-'));
            $lines[] = "* **Deskripsi:** {$desc}";
            $subjectSuffix = $request->input('subject', $subLabel);
        }

        $generatedSubject = "[{$serviceName}] " . ($subjectSuffix ?: $subLabel);
        $generatedDescription = implode("\n", $lines);

        return [
            $request->input('subject') ?: $generatedSubject,
            $generatedDescription,
        ];
    }

    /**
     * Detail & Tracking Pengaduan beserta riwayat tanggapan.
     */
    public function show(Ticket $ticket): View
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat tiket ini.');
        }

        $ticket->load(['category', 'user', 'responses.user', 'bsreDetail']);

        return view('tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Tambahkan tanggapan/balasan dari user ke pengaduan.
     */
    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'response_text' => ['required', 'string', 'min:2'],
        ], [
            'response_text.required' => 'Pesan tanggapan tidak boleh kosong.',
        ]);

        $ticket->responses()->create([
            'user_id' => $request->user()->id,
            'response_text' => $validated['response_text'],
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tanggapan Anda telah berhasil dikirim ke tim IT BBPOM.');
    }
}
