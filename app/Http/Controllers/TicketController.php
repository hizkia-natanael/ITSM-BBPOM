<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketResponse;
use App\Models\TicketResponseAttachment;
use App\Services\FileCompressionService;
use App\Services\Tickets\TicketAttachmentService;
use App\Services\Tickets\TicketCreationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        protected TicketCreationService $creationService,
        protected TicketAttachmentService $attachmentService,
    ) {}

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
            ->with(['category', 'responses', 'handledBy'])
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

        $query = $user->tickets()->with(['category', 'handledBy']);

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
                  ->orWhere('service_key', 'like', "%{$search}%")
                  ->orWhere('subcategory', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $tickets = $query->latest()->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('tickets.index', [
            'tickets' => $tickets,
            'categories' => $categories,
            'selectedStatus' => $request->status,
            'selectedCategory' => $request->category_id,
            'search' => $request->search,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Form Tambah Pengaduan (App Gateway & Dynamic Form).
     */
    public function create(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $services = config('services_gateway.services', []);
        foreach ($services as $key => &$service) {
            $cat = Category::resolveForService($key);
            $service['category_id'] = $cat?->id;
            $service['default_category_id'] = $cat?->id;
        }
        unset($service);

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
     * Simpan Pengaduan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->creationService->store($request);
    }

    /**
     * Detail & Tracking Pengaduan beserta riwayat tanggapan.
     */
    public function show(Ticket $ticket): View
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat tiket ini.');
        }

        $ticket->load(['attachments', 'category', 'user', 'handledBy', 'responses.user', 'responses.attachments', 'bsreDetail', 'zoomDetail', 'kegiatanDetail', 'windowsDetail', 'officeDetail', 'lainnyaDetail']);

        return view('tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Tambahkan tanggapan/balasan dari user ke pengaduan dengan dukungan berbagi berkas aman.
     */
    public function reply(Request $request, Ticket $ticket): RedirectResponse|\Illuminate\Http\JsonResponse
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

        // Collect uploaded files from both 'attachment' and 'attachments'
        $uploadedFiles = [];
        if ($request->hasFile('attachments')) {
            $filesInput = $request->file('attachments');
            if (is_array($filesInput)) {
                $uploadedFiles = array_values(array_filter($filesInput));
            }
        } elseif ($request->hasFile('attachment')) {
            $uploadedFiles = [$request->file('attachment')];
        }

        $responseText = trim((string) ($validated['response_text'] ?? ''));
        if ($responseText === '' && !empty($uploadedFiles)) {
            if (count($uploadedFiles) > 1) {
                $responseText = 'Melampirkan ' . count($uploadedFiles) . ' berkas (Arsip ZIP).';
            } else {
                $responseText = 'Melampirkan berkas: ' . $uploadedFiles[0]->getClientOriginalName();
            }
        }

        $replyToId = null;
        if (!empty($validated['reply_to_id'])) {
            $parentResponse = $ticket->responses()->where('id', $validated['reply_to_id'])->first();
            if ($parentResponse) {
                $replyToId = $parentResponse->id;
            }
        }

        $response = $ticket->responses()->create([
            'user_id' => $request->user()->id,
            'reply_to_id' => $replyToId,
            'response_text' => $responseText,
        ]);

        if (!empty($uploadedFiles)) {
            $compressionMode = $request->input('compression_mode', 'compressed');
            if (!in_array($compressionMode, ['compressed', 'original'])) {
                $compressionMode = 'compressed';
            }

            $fileService = app(FileCompressionService::class);
            $uploadData = $fileService->processMultipleUploads(
                $uploadedFiles,
                $ticket->id,
                $compressionMode
            );

            $response->attachments()->create([
                'file_path' => $uploadData['file_path'],
                'file_name' => $uploadData['file_name'],
                'file_size' => $uploadData['file_size'],
                'original_size' => $uploadData['original_size'],
                'mime_type' => $uploadData['mime_type'],
                'compression_mode' => $uploadData['compression_mode'],
                'is_compressed' => $uploadData['is_compressed'],
                'saved_bytes' => $uploadData['saved_bytes'],
            ]);
        }

        if ($request->user()->isAdmin()) {
            if (empty($ticket->handled_by_id)) {
                $ticket->handled_by_id = $request->user()->id;
            }
            if ($ticket->status === 'pending') {
                $ticket->status = 'diproses';
            }
            $ticket->save();
        }

        $response->loadMissing(['user', 'attachments', 'replyTo.user', 'replyTo.attachments']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $request->user()->isAdmin()
                    ? 'Tanggapan dan berkas Anda telah berhasil dikirim ke pelapor.'
                    : 'Tanggapan dan berkas Anda telah berhasil dikirim ke tim IT BBPOM.',
                'response' => $this->formatResponseForJson($response, (int) $request->user()->id),
            ]);
        }

        if ($request->user()->isAdmin() && (str_contains(url()->previous(), '/admin') || ($request->header('referer') && str_contains($request->header('referer'), '/admin')))) {
            return redirect()->back()
                ->with('success', 'Tanggapan dan berkas Anda telah berhasil dikirim ke pelapor.');
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tanggapan dan berkas Anda telah berhasil dikirim ke tim IT BBPOM.');
    }

    /**
     * Sinkronisasi pesan obrolan secara realtime (Smart Delta Polling).
     */
    public function chatSync(Request $request, Ticket $ticket): \Illuminate\Http\JsonResponse
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

        // Tandai pesan dari lawan bicara sebagai telah dibaca
        if ($responses->isNotEmpty()) {
            $ticket->responses()
                ->where('user_id', '!=', $currentUserId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $formatted = $responses->map(function (TicketResponse $res) use ($currentUserId) {
            return $this->formatResponseForJson($res, $currentUserId);
        });

        $hasAdminResponse = $ticket->responses()
            ->whereHas('user', function ($q) {
                $q->where('role', 'admin');
            })
            ->exists();

        return response()->json([
            'success' => true,
            'ticket_status' => $ticket->status,
            'handled_by' => $ticket->handledBy ? [
                'id' => $ticket->handledBy->id,
                'name' => $ticket->handledBy->name,
            ] : null,
            'has_admin_response' => $hasAdminResponse,
            'updated_at_formatted' => $ticket->updated_at->translatedFormat('d M Y, H:i') . ' WIB',
            'responses' => $formatted,
            'latest_id' => $responses->isNotEmpty() ? $responses->last()->id : $afterId,
        ]);
    }

    /**
     * Serialisasi respons obrolan untuk JSON payload realtime.
     */
    protected function formatResponseForJson(TicketResponse $res, int $currentUserId): array
    {
        $sender = $res->user;
        $isMe = ((int) $res->user_id === $currentUserId);
        $isSenderAdmin = $sender ? $sender->isAdmin() : false;

        $attachments = $res->attachments->map(function ($att) {
            return [
                'id' => $att->id,
                'file_name' => $att->file_name,
                'formatted_size' => $att->formatted_size,
                'extension_upper' => $att->extension_upper,
                'pure_icon_class' => $att->pure_icon_class,
                'document_hex_color' => $att->document_hex_color,
                'document_card_bg_hex' => $att->document_card_bg_hex,
                'document_card_border_hex' => $att->document_card_border_hex,
                'download_url' => $att->download_url,
                'is_compressed' => (bool) $att->is_compressed,
                'saving_percentage' => (int) $att->saving_percentage,
                'formatted_original_size' => $att->formatted_original_size,
            ];
        })->values()->all();

        $replyToData = null;
        if ($res->replyTo) {
            $parent = $res->replyTo;
            $parentSender = $parent->user;
            $parentIsAdmin = $parentSender ? $parentSender->isAdmin() : false;
            $parentFirstAtt = $parent->attachments->first();

            $replyToData = [
                'id' => $parent->id,
                'user_name' => $parentSender ? $parentSender->name : 'Pengguna',
                'user_role' => $parentIsAdmin ? 'Petugas IT' : 'Pemohon',
                'snippet' => $parent->snippet,
                'has_attachment' => $parentFirstAtt !== null,
                'attachment_name' => $parentFirstAtt?->file_name,
                'attachment_icon' => $parentFirstAtt?->pure_icon_class,
                'attachment_color' => $parentFirstAtt?->document_hex_color,
            ];
        }

        return [
            'id' => $res->id,
            'user_id' => $res->user_id,
            'user_name' => $sender ? $sender->name : 'Pengguna',
            'user_initials' => $sender ? strtoupper(substr($sender->name, 0, 2)) : '??',
            'is_me' => $isMe,
            'is_sender_admin' => $isSenderAdmin,
            'sender_badge' => $isMe
                ? ($isSenderAdmin ? 'Petugas IT (Anda)' : 'Pemohon (Anda)')
                : ($isSenderAdmin ? 'Petugas IT' : 'Pemohon'),
            'response_text' => $res->response_text,
            'time' => $res->created_at->format('H:i') . ' WIB',
            'date_key' => $res->created_at->format('Y-m-d'),
            'attachments' => $attachments,
            'reply_to' => $replyToData,
        ];
    }

    /**
     * Unduh atau pratinjau lampiran berkas dari tanggapan obrolan tiket secara aman.
     */
    public function downloadResponseAttachment(Request $request, Ticket $ticket, TicketResponseAttachment $attachment)
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh berkas tanggapan tiket ini.');
        }

        if ((int) $attachment->ticketResponse?->ticket_id !== (int) $ticket->id) {
            abort(404, 'Berkas lampiran tidak cocok dengan nomor tiket.');
        }

        $path = $attachment->file_path;
        $disk = config('filesystems.attachment_disk', 'local');

        if (!Storage::disk($disk)->exists($path)) {
            if (Storage::disk('local')->exists($path)) {
                $disk = 'local';
            } elseif (Storage::disk('public')->exists($path)) {
                $disk = 'public';
            } else {
                abort(404, 'Berkas fisik tidak ditemukan di server penyimpanan.');
            }
        }

        if ($request->boolean('inline')) {
            return Storage::disk($disk)->response($path, $attachment->file_name, [
                'Content-Type' => $attachment->mime_type ?: Storage::disk($disk)->mimeType($path),
            ]);
        }

        return Storage::disk($disk)->download($path, $attachment->file_name);
    }

    /**
     * Unduh atau pratinjau berkas lampiran tiket secara aman dengan verifikasi hak akses.
     */
    public function downloadAttachment(Request $request, Ticket $ticket, ?string $type = null)
    {
        return $this->attachmentService->downloadAttachment($request, $ticket, $type);
    }

    /**
     * Unduh atau pratinjau salah satu item lampiran dari banyak berkas tiket secara aman.
     */
    public function downloadAttachmentItem(Request $request, Ticket $ticket, TicketAttachment $attachment)
    {
        return $this->attachmentService->downloadAttachmentItem($request, $ticket, $attachment);
    }
}
