<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect('/admin')
            : redirect()->route('dashboard');
    }
    $users = \App\Models\User::select('id', 'name', 'nip', 'email', 'role')->orderBy('name')->get();
    return view('auth.login', compact('users'));
})->name('home');

Route::redirect('/admin/login', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    // User Portal Dashboard
    Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');

    // Tickets (Pengaduan)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/attachment/{type?}', [TicketController::class, 'downloadAttachment'])
        ->name('tickets.attachment');
    Route::get('/tickets/{ticket}/attachments/{attachment}', [TicketController::class, 'downloadAttachmentItem'])
        ->name('tickets.attachments.item');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])
        ->middleware('throttle:30,1')
        ->name('tickets.reply');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Web Push Notifications & Realtime Aduan Status
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/push/config', [\App\Http\Controllers\Admin\PushNotificationController::class, 'config'])->name('push.config');
    Route::post('/push/subscribe', [\App\Http\Controllers\Admin\PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\Admin\PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::post('/push/test', [\App\Http\Controllers\Admin\PushNotificationController::class, 'test'])->name('push.test');
    Route::get('/tickets/unhandled/status', [\App\Http\Controllers\Admin\PushNotificationController::class, 'unhandledStatus'])->name('tickets.unhandled.status');
    Route::post('/tickets/{ticket}/claim', [\App\Http\Controllers\Admin\PushNotificationController::class, 'claimTicket'])->name('tickets.claim');
    Route::get('/tickets/export', [\App\Http\Controllers\Admin\TicketExportController::class, 'export'])->name('tickets.export');
});

require __DIR__.'/auth.php';

