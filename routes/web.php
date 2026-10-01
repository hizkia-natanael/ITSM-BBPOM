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
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
