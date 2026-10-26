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
        if (! $this->app->runningInConsole()) {
            $appUrl = (string) config('app.url');
            $isDirectPort = in_array(request()->getPort(), [8000, 8085], true) || PHP_SAPI === 'cli-server';
            $isHelpdeskSubpath = str_contains($appUrl, '/helpdesk')
                || str_starts_with((string) request()->server('REQUEST_URI', ''), '/helpdesk');

            if ($isDirectPort || ! $isHelpdeskSubpath) {
                \Illuminate\Support\Facades\URL::useAssetOrigin(null);
            } else {
                $origin = in_array(request()->getHost(), ['192.168.1.254', '127.0.0.1', 'localhost'], true)
                    ? 'http://' . request()->getHttpHost() . '/helpdesk'
                    : $appUrl;

                \Illuminate\Support\Facades\URL::useOrigin($origin);
                \Illuminate\Support\Facades\URL::useAssetOrigin($origin);

                if (str_starts_with($origin, 'https://')) {
                    \Illuminate\Support\Facades\URL::forceScheme('https');
                }

                $basePath = rtrim((string) parse_url($origin, PHP_URL_PATH), '/');
                if ($basePath !== '') {
                    \Illuminate\Support\Facades\URL::formatPathUsing(
                        fn (string $path) => ($path === $basePath || str_starts_with($path, $basePath . '/'))
                            ? (substr($path, strlen($basePath)) ?: '/')
                            : $path
                    );
                }
            }
        }

        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
        \Carbon\Carbon::setLocale('id');

        if (PHP_OS_FAMILY === 'Windows' && ! getenv('OPENSSL_CONF')) {
            $candidate = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
            if (file_exists($candidate)) {
                putenv("OPENSSL_CONF={$candidate}");
            }
        }
    }
}
