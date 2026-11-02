<?php

namespace App\Services\Satisfaction;

use App\Models\Ticket;
use App\Models\TicketSatisfactionSurvey;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SatisfactionSurveyService
{
    /**
     * Ambil metrik indeks kepuasan pengguna agregat untuk display publik / portal.
     *
     * @return array{score: float, formatted: string, total_surveys: int, breakdown: array<string, float>}
     */
    public function getSatisfactionIndex(): array
    {
        $surveys = TicketSatisfactionSurvey::query();
        $total = $surveys->count();

        if ($total === 0) {
            return [
                'score' => 5.0,
                'formatted' => '5.0 / 5',
                'total_surveys' => 0,
                'breakdown' => [
                    'speed' => 5.0,
                    'quality' => 5.0,
                    'communication' => 5.0,
                    'system' => 5.0,
                ],
            ];
        }

        $avgScore = round((float) $surveys->avg('average_rating'), 1);
        $avgSpeed = round((float) $surveys->avg('speed_rating'), 1);
        $avgQuality = round((float) $surveys->avg('quality_rating'), 1);
        $avgComm = round((float) $surveys->avg('communication_rating'), 1);
        $avgSys = round((float) $surveys->avg('system_rating'), 1);

        return [
            'score' => $avgScore,
            'formatted' => number_format($avgScore, 1, '.', '') . ' / 5',
            'total_surveys' => $total,
            'breakdown' => [
                'speed' => $avgSpeed,
                'quality' => $avgQuality,
                'communication' => $avgComm,
                'system' => $avgSys,
            ],
        ];
    }

    /**
     * Simpan survei kepuasan untuk tiket yang telah diselesaikan.
     */
    public function submitSurvey(Ticket $ticket, User $user, array $data): TicketSatisfactionSurvey
    {
        if (!$ticket->canBeRatedByUser($user)) {
            throw ValidationException::withMessages([
                'ticket' => 'Tiket ini belum selesai atau sudah pernah diulas sebelumnya.',
            ]);
        }

        $speed = (int) $data['speed_rating'];
        $quality = (int) $data['quality_rating'];
        $communication = (int) $data['communication_rating'];
        $system = (int) $data['system_rating'];

        $average = round(($speed + $quality + $communication + $system) / 4, 2);

        return TicketSatisfactionSurvey::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'speed_rating' => $speed,
            'quality_rating' => $quality,
            'communication_rating' => $communication,
            'system_rating' => $system,
            'average_rating' => $average,
            'feedback' => !empty($data['feedback']) ? trim($data['feedback']) : null,
        ]);
    }
}
