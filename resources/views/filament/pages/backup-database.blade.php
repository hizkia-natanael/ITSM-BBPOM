<x-filament-panels::page>
    <x-backup.styles />
    <x-backup.modal-styles />

    <div class="bk-container" @if ($summary['has_running_job']) wire:poll.4s @endif>
        <x-backup.flash-alerts />

        <x-backup.header-banner :summary="$summary" :settings="$settings" />

        <x-backup.stats-overview :summary="$summary" :settings="$settings" />

        <x-backup.settings-card :settings="$settings" />

        <x-backup.history-table :backups="$backups" :retentionDays="$settings['retention_days']" />

        <x-backup.env-download-modal />
        <x-backup.error-detail-modal />
        <x-backup.delete-confirm-modal />
        <x-backup.disconnect-confirm-modal />
    </div>
</x-filament-panels::page>
