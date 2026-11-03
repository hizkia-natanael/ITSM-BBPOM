<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Support\TicketSampleData;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi tiket representatif untuk monitoring ITSM BBPOM Semarang.
     */
    public function run(): void
    {
        $users = User::all()->keyBy('email');
        $defaultUser = $users->firstWhere('role', 'user') ?? User::first();
        $defaultAdmin = $users->firstWhere('role', 'admin') ?? User::first();

        foreach (TicketSampleData::get() as $item) {
            $user = $users->get($item['user_email'], $defaultUser);
            $admin = $item['handled_by_email']
                ? $users->get($item['handled_by_email'], $defaultAdmin)
                : null;

            $isSelesai = $item['status'] === 'selesai';

            $ticket = Ticket::updateOrCreate(
                ['ticket_number' => $item['ticket_number']],
                [
                    'user_id' => $user->id,
                    'handled_by_id' => $admin?->id,
                    'accepted_by_id' => $admin?->id,
                    'accepted_at' => $admin ? $item['created_at']->copy()->addMinutes(10) : null,
                    'processed_by_id' => $admin?->id,
                    'processed_at' => $admin ? $item['created_at']->copy()->addMinutes(15) : null,
                    'resolved_by_id' => $isSelesai ? $admin?->id : null,
                    'resolved_at' => $isSelesai ? $item['updated_at'] : null,
                    'category_id' => $item['category_id'],
                    'service_key' => $item['service_key'],
                    'subcategory' => $item['subcategory'],
                    'subject' => $item['subject'],
                    'description' => $item['description'],
                    'status' => $item['status'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ]
            );

            foreach ($item['responses'] as $resp) {
                $sender = $users->get($resp['sender_email'], $defaultAdmin);
                TicketResponse::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'user_id' => $sender->id,
                        'response_text' => $resp['text'],
                    ],
                    [
                        'read_at' => Carbon::now(),
                        'created_at' => Carbon::now()->subMinutes($resp['minutes_ago']),
                        'updated_at' => Carbon::now()->subMinutes($resp['minutes_ago']),
                    ]
                );
            }
        }
    }
}
