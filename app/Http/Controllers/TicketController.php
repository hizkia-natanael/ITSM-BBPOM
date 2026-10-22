<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketResponseAttachment;
use App\Services\Tickets\TicketAttachmentService;
use App\Services\Tickets\TicketChatService;
use App\Services\Tickets\TicketCreationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class TicketController extends Controller
{
    public function __construct(
        protected TicketCreationService $creationService,
        protected TicketAttachmentService $attachmentService,
        protected TicketChatService $chatService,
    ) {}

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
        $recentTickets = $user->tickets()->with(['category', 'responses', 'handledBy'])->latest()->take(5)->get();
        return view('dashboard', compact('stats', 'recentTickets'));
    }

    public function index(Request $request): View
    {
        $query = $request->user()->tickets()->with(['category', 'handledBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($sub) => $sub
                ->where('ticket_number', 'like', "%{$request->search}%")
                ->orWhere('subject', 'like', "%{$request->search}%")
                ->orWhere('service_key', 'like', "%{$request->search}%")
                ->orWhere('subcategory', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")));
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50, 100], true) ? (int) $request->input('per_page', 10) : 10;
        return view('tickets.index', [
            'tickets' => $query->latest()->paginate($perPage)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'selectedStatus' => $request->status,
            'selectedCategory' => $request->category_id,
            'search' => $request->search,
            'perPage' => $perPage,
        ]);
    }

    public function create(Request $request): View
    {
        $services = config('services_gateway.services', []);
        foreach ($services as $key => &$service) {
            $catId = Category::resolveForService($key)?->id;
            $service['category_id'] = $catId;
            $service['default_category_id'] = $catId;
        }
        unset($service);
        return view('tickets.create', [
            'categories' => Category::orderBy('name')->get(),
            'services' => $services,
            'groups' => config('services_gateway.groups', []),
            'initialService' => $request->query('service'),
            'initialSubcategory' => $request->query('subcategory'),
            'user' => $request->user(),
            'todayDate' => Carbon::now()->format('Y-m-d'),
            'todayFormatted' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->creationService->store($request);
    }

    public function show(Ticket $ticket): View
    {
        abort_if($ticket->user_id !== auth()->id() && !auth()->user()->isAdmin(), 403, 'Anda tidak memiliki hak akses untuk melihat tiket ini.');
        $ticket->load(['attachments', 'category', 'user', 'handledBy', 'responses.user', 'responses.attachments', 'bsreDetail', 'zoomDetail', 'kegiatanDetail', 'windowsDetail', 'officeDetail', 'lainnyaDetail']);
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse|JsonResponse
    {
        return $this->chatService->reply($request, $ticket);
    }

    public function chatSync(Request $request, Ticket $ticket): JsonResponse
    {
        return $this->chatService->chatSync($request, $ticket);
    }

    public function downloadResponseAttachment(Request $request, Ticket $ticket, TicketResponseAttachment $attachment)
    {
        return $this->attachmentService->downloadResponseAttachment($request, $ticket, $attachment);
    }

    public function downloadAttachment(Request $request, Ticket $ticket, ?string $type = null)
    {
        return $this->attachmentService->downloadAttachment($request, $ticket, $type);
    }

    public function downloadAttachmentItem(Request $request, Ticket $ticket, TicketAttachment $attachment)
    {
        return $this->attachmentService->downloadAttachmentItem($request, $ticket, $attachment);
    }
}
