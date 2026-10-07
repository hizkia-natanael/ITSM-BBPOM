<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\TicketStatsOverview;
use App\Filament\Widgets\TicketTrendChart;
use App\Filament\Widgets\UnhandledTicketsAlertWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Admin Layanan TIK BBPOM Di Semarang')
            ->brandLogo(fn () => view('components.admin-brand-logo'))
            ->brandLogoHeight('2.5rem')
            ->topNavigation()
            ->globalSearch(false)
            ->darkMode(false)
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
                'warning' => Color::Amber,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
            ])
            ->font('Inter')
            ->favicon(asset('images/logo-bpom.png'))
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): View => view('components.admin-topbar-actions')
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): View => view('filament.hooks.head-scripts-styles')
            )
            ->renderHook(
                PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE,
                fn (): View => view('components.admin-tickets-auto-refresh'),
                scopes: \App\Filament\Resources\Tickets\Pages\ListTickets::class,
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                TicketStatsOverview::class,
                TicketTrendChart::class,
                UnhandledTicketsAlertWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
