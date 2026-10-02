<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class UnhandledTicketsAlertWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 5,
        'xl' => 5,
        '2xl' => 4,
    ];

    protected string $view = 'filament.widgets.unhandled-tickets-alert';

    protected ?string $pollingInterval = '15s';

    public function getUnhandledTickets(): Collection
    {
        return Ticket::whereNull('handled_by_id')
            ->where('status', 'pending')
            ->with(['user', 'category'])
            ->latest()
            ->take(6)
            ->get();
    }

    public function getUnhandledCount(): int
    {
        return Ticket::whereNull('handled_by_id')
            ->where('status', 'pending')
            ->count();
    }

    public function claimTicket(int $ticketId): void
    {
        $ticket = Ticket::whereNull('handled_by_id')
            ->where('status', 'pending')
            ->find($ticketId);

        if ($ticket) {
            $ticket->handled_by_id = auth()->id();
            $ticket->status = 'diproses';
            $ticket->save();

            Notification::make()
                ->title('Tiket Berhasil Diambil Alih')
                ->body("Aduan {$ticket->ticket_number} telah ditugaskan ke akun Anda (" . auth()->user()->name . ") dan berstatus Diproses.")
                ->success()
                ->send();

            $this->redirect(url("/admin/tickets/{$ticket->id}/edit"));
        }
    }
}
