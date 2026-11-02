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
        if (auth()->user()?->isKetuaTim()) {
            Notification::make()->title('Akses Monitoring')->body('Ketua Tim hanya memiliki hak akses pemantauan.')->warning()->send();
            return;
        }

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

    public function remindItTeam(?int $ticketId = null): void
    {
        $unhandledCount = $this->getUnhandledCount();
        if ($unhandledCount === 0) {
            Notification::make()->title('Semua Aduan Terkendali')->body('Tidak ada aduan pending yang memerlukan pengingat.')->info()->send();
            return;
        }

        $ticket = $ticketId ? Ticket::find($ticketId) : null;
        $ketuaName = auth()->user()?->name ?? 'Ketua Tim';
        $title = 'Pengingat dari Ketua Tim TI';
        $body = $ticket
            ? "[{$ticket->ticket_number}] {$ketuaName} mengingatkan: Aduan '{$ticket->subject}' belum ditangani!"
            : "{$ketuaName} mengingatkan: Masih ada {$unhandledCount} aduan pending yang belum ditangani.";
        $url = $ticket ? url("/admin/tickets/{$ticket->id}/edit") : url('/admin/tickets');

        try {
            app(\App\Services\WebPushService::class)->sendToAdmins($title, $body, $url, [
                'tag' => 'ketua-tim-reminder-' . ($ticket ? $ticket->id : 'all'),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Error webpush reminder: ' . $e->getMessage());
        }

        Notification::make()
            ->title('Pengingat Terkirim ke Tim TI')
            ->body($ticket ? "Aduan {$ticket->ticket_number} berhasil diingatkan ke seluruh Admin IT." : "Notifikasi pengingat {$unhandledCount} aduan telah disiarkan ke seluruh Admin IT.")
            ->success()
            ->send();
    }
}
