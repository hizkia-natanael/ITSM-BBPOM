<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (in_array($data['status'] ?? '', ['diproses', 'selesai', 'ditolak'])) {
            $data['handled_by_id'] = auth()->id();
        }

        return $data;
    }
}
