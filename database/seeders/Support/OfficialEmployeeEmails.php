<?php

namespace Database\Seeders\Support;

class OfficialEmployeeEmails
{
    /**
     * Mengembalikan seluruh pemetaan NIP => Email Resmi @pom.go.id (149 pegawai).
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return OfficialEmployeeEmailsPartA::get() + OfficialEmployeeEmailsPartB::get();
    }

    /**
     * Mengambil email resmi berdasarkan NIP, atau fallback ke email awal jika tidak terdaftar.
     */
    public static function resolve(string $nip, string $fallbackEmail): string
    {
        $map = self::all();

        return $map[$nip] ?? $fallbackEmail;
    }
}
