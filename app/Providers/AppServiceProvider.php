<?php

namespace App\Providers;

use Carbon\Carbon;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LogoutResponse::class,
            fn () => new class implements LogoutResponse {
                public function toResponse($request): RedirectResponse
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
        if (! $this->app->runningInConsole()) {
            $this->configureUrlAndProxyRouting();
        }

        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
        Carbon::setLocale('id');

        if (PHP_OS_FAMILY === 'Windows' && ! getenv('OPENSSL_CONF')) {
            $candidate = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
            if (file_exists($candidate)) {
                putenv("OPENSSL_CONF={$candidate}");
            }
        }
    }

    /**
     * Configure URL and asset schemes for direct IP access and domain reverse proxying.
     */
    protected function configureUrlAndProxyRouting(): void
    {
        $host = (string) request()->getHost();
        $forwardedHost = (string) request()->header('x-forwarded-host', '');
        $forwardedServer = (string) request()->header('x-forwarded-server', '');

        $isDomainRequest = str_contains($host, 'lawangsewu.bbpomsemarang.id')
            || str_contains($forwardedHost, 'lawangsewu.bbpomsemarang.id')
            || str_contains($forwardedServer, 'lawangsewu.bbpomsemarang.id');

        $isDirectLocalIp = str_starts_with($host, '192.168.')
            || (($host === '127.0.0.1' || $host === 'localhost') && $forwardedHost === '');

        if ($isDirectLocalIp && ! $isDomainRequest) {
            URL::useOrigin(null);
            URL::useAssetOrigin(null);
            return;
        }

        $origin = 'https://lawangsewu.bbpomsemarang.id/elite';
        URL::forceScheme('https');
        URL::useOrigin($origin);
        URL::useAssetOrigin($origin);

        URL::formatPathUsing(function (string $path) {
            if (preg_match('#^/elite(/.*)?$#i', $path, $matches)) {
                return ! empty($matches[1]) ? $matches[1] : '/';
            }
            return $path;
        });
    }
}
