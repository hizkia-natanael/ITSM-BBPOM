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
        if (auth()->user()?->isKetuaTim()) {
            abort(403, 'Akses ditolak: Ketua Tim hanya memiliki wewenang monitoring.');
        }

        $status = $data['status'] ?? '';
        $actorId = auth()->id();

        if ($status === 'diterima') {
            $data['accepted_by_id'] = $this->record->accepted_by_id ?: $actorId;
            $data['accepted_at'] = $this->record->accepted_at ?: now();
            $data['handled_by_id'] = $actorId;
        } elseif ($status === 'diproses') {
            $data['processed_by_id'] = $actorId;
            $data['processed_at'] = now();
            $data['handled_by_id'] = $actorId;
            if (!$this->record->accepted_at) {
                $data['accepted_by_id'] = $this->record->accepted_by_id ?: $actorId;
                $data['accepted_at'] = now();
            }
        } elseif (in_array($status, ['selesai', 'ditolak'], true)) {
            $data['resolved_by_id'] = $actorId;
            $data['resolved_at'] = now();
            $data['handled_by_id'] = $actorId;
        } elseif ($status === 'pending') {
            $data['handled_by_id'] = null;
        }

        return $data;
    }
}
