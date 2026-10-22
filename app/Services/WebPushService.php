<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\TicketResponse;
use App\Models\User;
use App\Services\Notifications\WebPushTransportDispatcher;
use Illuminate\Support\Str;

class WebPushService
{
    protected WebPushTransportDispatcher $transport;

    public function __construct(?WebPushTransportDispatcher $transport = null)
    {
        $this->transport = $transport ?? new WebPushTransportDispatcher();
    }

    public function getVapidPublicKey(): string
    {
        return config('webpush.vapid.public_key', '');
    }

    public function sendTicketNotification(Ticket $ticket): int
    {
        $title = 'Aduan Masuk Belum Ditangani!';
        $pelapor = $ticket->user?->name ?? 'Pegawai BBPOM';
        $body = "[{$ticket->ticket_number}] {$pelapor}: {$ticket->subject} (Menunggu tanggapan tim IT)";
        $url = url('/admin/tickets/' . $ticket->id . '/edit');
        return $this->sendToAdmins($title, $body, $url, [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'tag' => 'unhandled-ticket-' . $ticket->id,
        ]);
    }

    public function sendStatusNotification(Ticket $ticket, TicketEvent $event): int
    {
        [$title, $message] = match ($event->status) {
            'diproses' => ['Tiket Sedang Diproses', 'Tim IT mulai menangani pengaduan Anda.'],
            'selesai' => ['Tiket Selesai', 'Pengaduan Anda telah diselesaikan oleh tim IT.'],
            'ditolak' => ['Tiket Ditolak', 'Pengaduan Anda tidak dapat ditindaklanjuti. Buka tiket untuk melihat alasannya.'],
            default => ['Status Tiket Diperbarui', 'Status pengaduan Anda berubah.'],
        };
        $body = "[{$ticket->ticket_number}] {$ticket->subject} - {$message}";
        return $this->sendToUsers([$ticket->user_id], $title, $body, route('tickets.show', $ticket), [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'tag' => 'ticket-status-' . $ticket->id,
        ]);
    }

    public function sendReplyNotification(TicketResponse $response): int
    {
        $response->loadMissing(['ticket.user', 'user']);
        $ticket = $response->ticket;
        $sender = $response->user;
        if (!$ticket || !$sender) {
            return 0;
        }
        $snippet = Str::limit(trim((string) preg_replace('/\s+/', ' ', $response->response_text)), 100);
        if ($sender->isAdmin()) {
            $recipientIds = [$ticket->user_id];
            $title = 'Balasan Baru dari Tim IT';
            $url = route('tickets.show', $ticket);
        } else {
            $recipientIds = $ticket->handled_by_id
                ? [$ticket->handled_by_id]
                : User::where('role', 'admin')->pluck('id')->all();
            $title = 'Pesan Baru dari Pelapor';
            $url = url('/admin/tickets/' . $ticket->id . '/edit');
        }
        $body = "[{$ticket->ticket_number}] {$sender->name}: {$snippet}";
        $recipientIds = array_values(array_diff($recipientIds, [$sender->id]));
        return $this->sendToUsers($recipientIds, $title, $body, $url, [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'tag' => 'ticket-reply-' . $ticket->id,
        ]);
    }

    public function sendToUsers(array $userIds, string $title, string $body, ?string $url = null, array $extra = []): int
    {
        if (empty($userIds)) {
            return 0;
        }
        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();
        return $subscriptions->isEmpty() ? 0 : $this->dispatchNotifications($subscriptions, $title, $body, $url, $extra);
    }

    public function sendToAdmins(string $title, string $body, ?string $url = null, array $extra = []): int
    {
        $adminIds = User::where('role', 'admin')->pluck('id');
        $subscriptions = PushSubscription::whereIn('user_id', $adminIds)->get();
        return $subscriptions->isEmpty() ? 0 : $this->dispatchNotifications($subscriptions, $title, $body, $url, $extra);
    }

    public function sendTestNotificationToUser(User $user): int
    {
        $subscriptions = $user->pushSubscriptions;
        if ($subscriptions->isEmpty()) {
            return 0;
        }
        return $this->dispatchNotifications(
            $subscriptions,
            'Notifikasi Desktop BBPOM Aktif',
            'Sistem Web Push berhasil terhubung! Anda akan menerima pop-up desktop saat ada aduan baru.',
            url('/admin'),
            ['tag' => 'test-notification']
        );
    }

    protected function dispatchNotifications($subscriptions, string $title, string $body, ?string $url = null, array $extra = []): int
    {
        return $this->transport->dispatch($subscriptions, $title, $body, $url, $extra);
    }
}
