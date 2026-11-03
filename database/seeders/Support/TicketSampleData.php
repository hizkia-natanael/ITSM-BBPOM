<?php

namespace Database\Seeders\Support;

class TicketSampleData
{
    /**
     * Menggabungkan tiket aktif dan tiket selesai representatif ITSM BBPOM.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function get(): array
    {
        return array_merge(
            TicketActiveSampleData::get(),
            TicketCompletedSampleData::get()
        );
    }
}
