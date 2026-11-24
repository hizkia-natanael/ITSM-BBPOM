<?php

use App\Models\BackupSetting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('elite:backup-database --trigger=scheduled')
    ->dailyAt(BackupSetting::scheduledTimeSafe())
    ->timezone('Asia/Jakarta')
    ->when(fn () => BackupSetting::isAutoBackupEnabledSafe())
    ->withoutOverlapping();

