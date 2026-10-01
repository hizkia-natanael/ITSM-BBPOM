<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\TicketStatsOverview;
use App\Filament\Widgets\TicketTrendChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->brandName('Layanan Admin TIK BBPOM Di Semarang')
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
            ->favicon(null)
            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn (): \Illuminate\Contracts\View\View => view('components.admin-logout-button')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('
                    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
                    <script>
                        window.filamentChartJsGlobalPlugins = window.filamentChartJsGlobalPlugins || [];
                        window.filamentChartJsGlobalPlugins.push({
                            id: "bbpomSoloLegendPlugin",
                            beforeInit: function (chart) {
                                if (!chart.options) chart.options = {};
                                if (!chart.options.plugins) chart.options.plugins = {};
                                if (!chart.options.plugins.legend) chart.options.plugins.legend = {};

                                chart.options.plugins.legend.onClick = function (e, legendItem, legend) {
                                    const c = legend.chart;
                                    const clickedIdx = legendItem.datasetIndex;

                                    let visibleCount = 0;
                                    let singleVisibleIdx = -1;
                                    for (let i = 0; i < c.data.datasets.length; i++) {
                                        if (c.isDatasetVisible(i)) {
                                            visibleCount++;
                                            singleVisibleIdx = i;
                                        }
                                    }

                                    if (visibleCount === 1 && singleVisibleIdx === clickedIdx) {
                                        for (let i = 0; i < c.data.datasets.length; i++) {
                                            c.setDatasetVisibility(i, true);
                                        }
                                    } else {
                                        for (let i = 0; i < c.data.datasets.length; i++) {
                                            c.setDatasetVisibility(i, i === clickedIdx);
                                        }
                                    }
                                    c.update();
                                };
                            }
                        });
                    </script>
                    <style>
                        /* BBPOM Fixed Light Mode & Design System Enhancements */
                        body {
                            background-color: #f8fafc !important;
                            color: #1e293b !important;
                        }
                        .fi-main-ctn {
                            background-color: #f8fafc !important;
                        }
                        .fi-sidebar {
                            background-color: #ffffff !important;
                            border-right-color: rgba(226, 232, 240, 0.9) !important;
                        }
                        /* Navbar & Brandmark alignment fix */
                        .fi-logo,
                        .fi-logo-light {
                            display: inline-flex !important;
                            flex-direction: row !important;
                            align-items: center !important;
                            height: auto !important;
                            max-height: 2.75rem !important;
                            text-decoration: none !important;
                        }
                        .fi-sidebar-header-logo-ctn a,
                        .fi-topbar-start a {
                            display: inline-flex !important;
                            flex-direction: row !important;
                            align-items: center !important;
                            text-decoration: none !important;
                        }
                        .fi-sidebar-header {
                            height: 4rem !important;
                            min-height: 4rem !important;
                            display: flex !important;
                            align-items: center !important;
                            padding-left: 1.25rem !important;
                            padding-right: 1.25rem !important;
                            background-color: #ffffff !important;
                            border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
                            overflow: visible !important;
                        }
                        /* Navbar & Top Navigation Styling */
                        .fi-topbar {
                            height: 4.25rem !important;
                            min-height: 4.25rem !important;
                            display: flex !important;
                            align-items: center !important;
                            padding-left: 1.5rem !important;
                            padding-right: 1.5rem !important;
                            background-color: rgba(255, 255, 255, 0.98) !important;
                            backdrop-filter: blur(12px) !important;
                            border-bottom: 1px solid rgba(226, 232, 240, 0.85) !important;
                            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04) !important;
                        }
                        @media (min-width: 1024px) {
                            .fi-topbar-start {
                                display: flex !important;
                                align-items: center !important;
                                border-right: 1px solid rgba(226, 232, 240, 0.85) !important;
                                padding-right: 1.25rem !important;
                                margin-right: 0.75rem !important;
                            }
                        }
                        .fi-topbar-nav-groups {
                            display: flex !important;
                            align-items: center !important;
                            gap: 0.35rem !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            list-style: none !important;
                        }
                        .fi-topbar-item-btn {
                            display: inline-flex !important;
                            align-items: center !important;
                            gap: 0.45rem !important;
                            padding: 0.45rem 0.85rem !important;
                            border-radius: 0.625rem !important;
                            font-size: 0.8125rem !important;
                            font-weight: 600 !important;
                            color: #475569 !important;
                            transition: all 0.15s ease-in-out !important;
                            text-decoration: none !important;
                        }
                        .fi-topbar-item-btn:hover {
                            background-color: #ecfdf5 !important;
                            color: #047857 !important;
                        }
                        .fi-topbar-item.fi-active .fi-topbar-item-btn {
                            background-color: #ecfdf5 !important;
                            color: #065f46 !important;
                            font-weight: 700 !important;
                        }
                        .fi-topbar-item.fi-active .fi-topbar-item-label {
                            color: #065f46 !important;
                            font-weight: 700 !important;
                        }
                        .fi-topbar-item.fi-active .fi-icon {
                            color: #059669 !important;
                        }
                        .fi-topbar-end {
                            display: flex !important;
                            align-items: center !important;
                            gap: 0.75rem !important;
                            margin-left: auto !important;
                        }
                        /* Full-width container when top navigation is active */
                        .fi-body-has-top-navigation .fi-sidebar {
                            display: none !important;
                        }
                        .fi-body-has-top-navigation .fi-main-ctn {
                            margin-left: 0 !important;
                            padding: 1.75rem 1.5rem !important;
                            max-width: 90rem !important;
                            margin: 0 auto !important;
                        }
                        .fi-section {
                            border-color: rgba(226, 232, 240, 0.85) !important;
                            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04) !important;
                            border-radius: 1rem !important;
                        }
                        .fi-wi-stats-overview-stat {
                            border-radius: 1rem !important;
                            border-color: rgba(226, 232, 240, 0.85) !important;
                            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04) !important;
                            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
                        }
                        .fi-wi-stats-overview-stat:hover {
                            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05) !important;
                        }
                    </style>
                ')
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
