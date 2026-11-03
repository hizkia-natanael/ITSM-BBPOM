<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketSatisfactionSurvey;
use Database\Seeders\Support\SurveySampleData;
use Illuminate\Database\Seeder;

class TicketSatisfactionSurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi survei kepuasan pelanggan dengan rating bintang 5 sempurna (5.0 / 5.0).
     */
    public function run(): void
    {
        $surveysData = SurveySampleData::get();

        foreach ($surveysData as $ticketNumber => $data) {
            $ticket = Ticket::where('ticket_number', $ticketNumber)->first();

            if (!$ticket) {
                continue;
            }

            TicketSatisfactionSurvey::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'user_id' => $ticket->user_id,
                    'speed_rating' => $data['speed_rating'],
                    'quality_rating' => $data['quality_rating'],
                    'communication_rating' => $data['communication_rating'],
                    'system_rating' => $data['system_rating'],
                    'average_rating' => $data['average_rating'],
                    'feedback' => $data['feedback'],
                    'created_at' => $ticket->resolved_at ?? $ticket->updated_at,
                    'updated_at' => $ticket->resolved_at ?? $ticket->updated_at,
                ]
            );
        }
    }
}
