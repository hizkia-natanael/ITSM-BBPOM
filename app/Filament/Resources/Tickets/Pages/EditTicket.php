<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Services\Export\TicketQueryBuilder;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['description'])) {
            $data['description'] = TicketQueryBuilder::stripMarkdown($data['description']);
        }
        if (isset($data['subject'])) {
            $data['subject'] = TicketQueryBuilder::stripMarkdown($data['subject']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Otomatis mencatat akun admin yang sedang login (Admin 1 / Admin 2) saat mengubah status
        if (in_array($data['status'] ?? '', ['diproses', 'selesai', 'ditolak'])) {
            $data['handled_by_id'] = auth()->id();
        } elseif (($data['status'] ?? '') === 'pending') {
            $data['handled_by_id'] = null;
        }

        return $data;
    }
}
