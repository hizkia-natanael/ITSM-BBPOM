<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            fn () => new class implements \Filament\Auth\Http\Responses\Contracts\LogoutResponse {
                public function toResponse($request): \Illuminate\Http\RedirectResponse
                {
                    return redirect('/login');
                }
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
        \Carbon\Carbon::setLocale('id');
    }
}
