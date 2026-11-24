<x-filament-panels::page>
    <x-backup.styles />

    <div class="bk-container">
        {{-- Flash Notification Alerts --}}
        <x-backup.flash-alerts />

        {{-- Header Status & Action Bar --}}
        <x-backup.header-banner :summary="$summary" :settings="$settings" />

        {{-- 4 Metric Cards Overview --}}
        <x-backup.stats-overview :summary="$summary" :settings="$settings" />

        {{-- Full-width Card Pengaturan Backup & Google Drive --}}
        <x-backup.settings-card :settings="$settings" />

        {{-- Full-width Riwayat Cadangan Database Table --}}
        <x-backup.history-table :backups="$backups" />

        {{-- Modal Download .env Terenkripsi --}}
        <x-backup.env-download-modal />
    </div>
</x-filament-panels::page>
