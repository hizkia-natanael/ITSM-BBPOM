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

Route::get('/admin/login', fn () => redirect()->route('login'));

Route::get('/presence/online', [\App\Http\Controllers\OnlinePresenceController::class, 'status'])
    ->middleware('throttle:60,1')
    ->name('presence.online');

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
    Route::get('/tickets/{ticket}/response-attachments/{attachment}', [TicketController::class, 'downloadResponseAttachment'])
        ->name('tickets.response-attachments.download');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])
        ->middleware('throttle:30,1')
        ->name('tickets.reply');
    Route::get('/tickets/{ticket}/chat-sync', [TicketController::class, 'chatSync'])
        ->name('tickets.chat-sync');

    // User Notification Bell (balasan tim IT & status) & Web Push opt-in
    Route::get('/notifications/replies', [\App\Http\Controllers\UserNotificationController::class, 'replies'])->name('notifications.replies');
    Route::post('/notifications/replies/read', [\App\Http\Controllers\UserNotificationController::class, 'markAllRead'])->name('notifications.replies.read');
    Route::post('/notifications/read-item', [\App\Http\Controllers\UserNotificationController::class, 'markItemRead'])->name('notifications.read-item');
    Route::get('/push/config', [\App\Http\Controllers\Admin\PushNotificationController::class, 'config'])->name('user.push.config');
    Route::post('/push/subscribe', [\App\Http\Controllers\Admin\PushNotificationController::class, 'subscribe'])->name('user.push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\Admin\PushNotificationController::class, 'unsubscribe'])->name('user.push.unsubscribe');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Buku Panduan Interaktif ITSM BBPOM
    Route::get('/panduan', [\App\Http\Controllers\GuideBookController::class, 'index'])->name('guide.index');
    Route::get('/guide-book', fn () => redirect()->route('guide.index'));
});

// Admin Web Push Notifications & Realtime Aduan Status
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/push/config', [\App\Http\Controllers\Admin\PushNotificationController::class, 'config'])->name('push.config');
    Route::post('/push/subscribe', [\App\Http\Controllers\Admin\PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\Admin\PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::post('/push/test', [\App\Http\Controllers\Admin\PushNotificationController::class, 'test'])->name('push.test');
    Route::get('/tickets/unhandled/status', [\App\Http\Controllers\Admin\PushNotificationController::class, 'unhandledStatus'])->name('tickets.unhandled.status');
    Route::get('/tickets/realtime-check', [\App\Http\Controllers\Admin\PushNotificationController::class, 'realtimeCheck'])->name('tickets.realtime-check');
    Route::post('/notifications/read-item', [\App\Http\Controllers\Admin\PushNotificationController::class, 'markItemRead'])->name('notifications.read-item');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\PushNotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/tickets/{ticket}/claim', [\App\Http\Controllers\Admin\PushNotificationController::class, 'claimTicket'])->name('tickets.claim');
    Route::get('/tickets/export', [\App\Http\Controllers\Admin\TicketExportController::class, 'export'])->name('tickets.export');
});

require __DIR__.'/auth.php';

