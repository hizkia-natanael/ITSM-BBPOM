<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Services\Tickets\TicketAttachmentService;
use App\Services\Tickets\TicketCreationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $ticket->load(['category', 'user', 'handledBy', 'responses.user', 'bsreDetail', 'zoomDetail', 'kegiatanDetail', 'windowsDetail', 'officeDetail', 'lainnyaDetail']);

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
            'response_text' => trim($validated['response_text']),
        ]);

        if ($request->user()->isAdmin()) {
            if (empty($ticket->handled_by_id)) {
                $ticket->handled_by_id = $request->user()->id;
            }
            if ($ticket->status === 'pending') {
                $ticket->status = 'diproses';
            }
            $ticket->save();
        }

        if ($request->user()->isAdmin() && (str_contains(url()->previous(), '/admin') || ($request->header('referer') && str_contains($request->header('referer'), '/admin')))) {
            return redirect()->back()
                ->with('success', 'Tanggapan Anda telah berhasil dikirim ke pelapor.');
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tanggapan Anda telah berhasil dikirim ke tim IT BBPOM.');
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
