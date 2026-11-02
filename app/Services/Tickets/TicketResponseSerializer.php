<?php

namespace App\Services\Tickets;

use App\Models\TicketResponse;

class TicketResponseSerializer
{
    /**
     * Serialisasi respons obrolan untuk JSON payload realtime.
     */
    public function format(TicketResponse $res, int $currentUserId): array
    {
        $sender = $res->user;
        $isMe = ((int) $res->user_id === $currentUserId);
        $isSenderAdmin = $sender ? $sender->isAdmin() : false;

        $attachments = $res->attachments->map(fn ($att) => [
            'id' => $att->id,
            'file_name' => $att->file_name,
            'formatted_size' => $att->formatted_size,
            'extension_upper' => $att->extension_upper,
            'pure_icon_class' => $att->pure_icon_class,
            'document_hex_color' => $att->document_hex_color,
            'document_card_bg_hex' => $att->document_card_bg_hex,
            'document_card_border_hex' => $att->document_card_border_hex,
            'download_url' => $att->download_url,
            'is_compressed' => (bool) $att->is_compressed,
            'saving_percentage' => (int) $att->saving_percentage,
            'formatted_original_size' => $att->formatted_original_size,
        ])->values()->all();

        $replyToData = null;
        if ($res->replyTo) {
            $parent = $res->replyTo;
            $parentSender = $parent->user;
            $parentIsAdmin = $parentSender ? $parentSender->isAdmin() : false;
            $parentFirstAtt = $parent->attachments->first();

            $replyToData = [
                'id' => $parent->id,
                'user_name' => $parentSender ? $parentSender->name : 'Pengguna',
                'user_role' => $parentIsAdmin ? 'Petugas IT' : 'Pemohon',
                'snippet' => $parent->snippet,
                'has_attachment' => $parentFirstAtt !== null,
                'attachment_name' => $parentFirstAtt?->file_name,
                'attachment_icon' => $parentFirstAtt?->pure_icon_class,
                'attachment_color' => $parentFirstAtt?->document_hex_color,
            ];
        }

        return [
            'id' => $res->id,
            'user_id' => $res->user_id,
            'user_name' => $sender ? $sender->name : 'Pengguna',
            'user_initials' => $sender ? strtoupper(substr($sender->name, 0, 2)) : '??',
            'is_me' => $isMe,
            'is_sender_admin' => $isSenderAdmin,
            'sender_badge' => $isMe
                ? ($isSenderAdmin ? 'Petugas IT (Anda)' : 'Pemohon (Anda)')
                : ($isSenderAdmin ? 'Petugas IT' : 'Pemohon'),
            'response_text' => $res->response_text,
            'time' => $res->created_at->format('H:i') . ' WIB',
            'date_key' => $res->created_at->format('Y-m-d'),
            'attachments' => $attachments,
            'reply_to' => $replyToData,
        ];
    }
}
