<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\TicketStatsOverview;
use App\Filament\Widgets\TicketTrendChart;
use App\Filament\Widgets\UnhandledTicketsAlertWidget;
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
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn (): \Illuminate\Contracts\View\View => view('components.admin-topbar-actions')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('
                    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
                    <script>
                        document.title = "Admin Layanan TIK BBPOM Di Semarang";
                        document.addEventListener("livewire:navigated", function () {
                            document.title = "Admin Layanan TIK BBPOM Di Semarang";
                        });
                        window.addEventListener("DOMContentLoaded", function () {
                            document.title = "Admin Layanan TIK BBPOM Di Semarang";
                        });

                        window.bbpomTicketConversation = function bbpomTicketConversation() {
                            return {
                                isSubmitting: false,
                                init() {
                                    this.$nextTick(() => {
                                        this.scrollToBottom();
                                        this.adjustTextareaHeight();
                                    });
                                },
                                scrollToBottom() {
                                    if (this.$refs.streamContainer) {
                                        this.$refs.streamContainer.scrollTop = this.$refs.streamContainer.scrollHeight;
                                    }
                                },
                                adjustTextareaHeight() {
                                    const el = this.$refs.replyTextarea;
                                    if (!el) return;
                                    el.style.height = "auto";
                                    if (el.scrollHeight > 128) {
                                        el.style.height = "128px";
                                        el.style.overflowY = "auto";
                                    } else {
                                        el.style.height = el.scrollHeight + "px";
                                        el.style.overflowY = "hidden";
                                    }
                                },
                                onFormSubmit(e) {
                                    const el = this.$refs.replyTextarea;
                                    if (!el || el.value.trim().length === 0) {
                                        if (e) e.preventDefault();
                                        return false;
                                    }
                                    if (this.isSubmitting) {
                                        if (e) e.preventDefault();
                                        return false;
                                    }
                                    this.isSubmitting = true;
                                    return true;
                                },
                                handleEnterSend(e) {
                                    if (e && e.shiftKey) {
                                        return;
                                    }
                                    if (e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                    }
                                    const el = this.$refs.replyTextarea;
                                    const form = this.$refs.replyForm;
                                    if (!el || !form) return;

                                    const text = el.value.trim();
                                    if (text.length === 0) return;

                                    if (this.isSubmitting) return;
                                    this.isSubmitting = true;

                                    form.submit();
                                }
                            };
                        };
                    </script>
                    <script>
                        window.filamentChartJsGlobalPlugins = window.filamentChartJsGlobalPlugins || [];
                        window.filamentChartJsGlobalPlugins.push({
                            id: "bbpomSmoothAnimationPlugin",
                            beforeInit: function (chart) {
                                if (!chart.options) chart.options = {};
                                chart.options.animation = Object.assign({
                                    duration: 500,
                                    easing: "easeOutQuart"
                                }, chart.options.animation);
                                chart.options.transitions = Object.assign({
                                    resize: {
                                        animation: {
                                            duration: 450,
                                            easing: "easeOutQuart"
                                        }
                                    }
                                }, chart.options.transitions);
                            }
                        });
                        window.filamentChartJsGlobalPlugins.push({
                            id: "bbpomChartBridgePlugin",
                            beforeInit: function (chart) {
                                if (chart && chart.canvas) {
                                    chart.canvas.__bbpomChart = chart;
                                }
                                window.__bbpomChart = chart;
                            },
                            afterUpdate: function (chart) {
                                if (chart && chart.canvas) {
                                    chart.canvas.__bbpomChart = chart;
                                }
                                window.__bbpomChart = chart;
                            }
                        });

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

                        window.bbpomTrendStatusController = function(initialFilter) {
                            return {
                                activeFilter: initialFilter || "all",
                                getChart() {
                                    if (window.__bbpomChart) return window.__bbpomChart;
                                    const canvas = document.querySelector(".fi-wi-chart canvas");
                                    if (canvas && canvas.__bbpomChart) return canvas.__bbpomChart;
                                    if (typeof Chart !== "undefined" && Chart.getChart && canvas) {
                                        return Chart.getChart(canvas);
                                    }
                                    return null;
                                },
                                onHover(status) {
                                    if (this.activeFilter === status) return;
                                    this.activeFilter = status;
                                    this.applyToChart(status);
                                },
                                onClick(status) {
                                    if (this.activeFilter === status && status !== "all") {
                                        this.activeFilter = "all";
                                        this.applyToChart("all");
                                    } else {
                                        this.activeFilter = status;
                                        this.applyToChart(status);
                                    }
                                },
                                applyToChart(status) {
                                    const chart = this.getChart();
                                    if (!chart) return;
                                    const map = { "pending": 0, "diproses": 1, "selesai": 2, "ditolak": 3 };
                                    if (status === "all") {
                                        for (let i = 0; i < 4; i++) {
                                            chart.setDatasetVisibility(i, true);
                                        }
                                    } else if (map[status] !== undefined) {
                                        for (let i = 0; i < 4; i++) {
                                            chart.setDatasetVisibility(i, i === map[status]);
                                        }
                                    }
                                    chart.update();
                                }
                            };
                        };
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
                        /* Ticket detail 2-column flex alignment */
                        .ticket-form-column {
                            display: flex !important;
                            flex-direction: column !important;
                            height: 100% !important;
                        }
                        .ticket-form-column > .fi-section {
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .ticket-form-column > .fi-section.flex-1 {
                            flex: 1 1 0% !important;
                        }
                        .ticket-form-column > .fi-section > .fi-section-content-ctn {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .ticket-form-column > .fi-section > .fi-section-content-ctn > .fi-section-content {
                            flex: 1 1 0% !important;
                        }
                        .ticket-form-card-full {
                            height: 100% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .ticket-form-card-full > .fi-section-content-ctn {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .ticket-form-card-full > .fi-section-content-ctn > .fi-section-content {
                            flex: 1 1 0% !important;
                        }
                        /* Dashboard Equal-Height Side-by-Side Widgets */
                        .fi-wi-grid {
                            align-items: stretch !important;
                        }
                        .fi-wi-chart {
                            height: 100% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .fi-wi-chart > .fi-section {
                            height: 100% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .fi-wi-chart > .fi-section > .fi-section-content-ctn {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .fi-wi-chart > .fi-section > .fi-section-content-ctn > .fi-section-content {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .fi-wi-unhandled-tickets-alert {
                            height: 100% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .fi-wi-unhandled-tickets-alert > .fi-section {
                            height: 100% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .fi-wi-unhandled-tickets-alert > .fi-section > .fi-section-content-ctn {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .fi-wi-unhandled-tickets-alert > .fi-section > .fi-section-content-ctn > .fi-section-content {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                            padding: 0.75rem 1rem !important;
                        }
                        .fi-wi-chart > .fi-section > .fi-section-content-ctn > .fi-section-content {
                            padding: 0.75rem 1rem !important;
                        }

                        /* BBPOM Option 2 Minimalist Status Chips */
                        .bbpom-status-chips-wrap {
                            display: flex !important;
                            flex-direction: row !important;
                            flex-wrap: wrap !important;
                            align-items: center !important;
                            gap: 0.45rem !important;
                            margin-bottom: 0.75rem !important;
                            padding-bottom: 0.65rem !important;
                            border-bottom: 1px solid #f1f5f9 !important;
                        }
                        .bbpom-status-chip {
                            display: inline-flex !important;
                            align-items: center !important;
                            gap: 0.35rem !important;
                            padding: 0.32rem 0.68rem !important;
                            border-radius: 9999px !important;
                            font-size: 0.75rem !important;
                            font-weight: 600 !important;
                            cursor: pointer !important;
                            white-space: nowrap !important;
                            line-height: 1 !important;
                            border: 1px solid !important;
                            transition: all 0.15s ease-in-out !important;
                            user-select: none !important;
                            outline: none !important;
                            text-decoration: none !important;
                        }
                        .bbpom-status-chip .chip-dot {
                            display: inline-block !important;
                            width: 0.45rem !important;
                            height: 0.45rem !important;
                            border-radius: 9999px !important;
                            flex-shrink: 0 !important;
                            transition: background-color 0.15s ease !important;
                        }
                        .bbpom-status-chip .chip-count {
                            display: inline-flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            padding: 0.1rem 0.45rem !important;
                            border-radius: 9999px !important;
                            font-size: 0.6875rem !important;
                            font-weight: 700 !important;
                            line-height: 1 !important;
                            flex-shrink: 0 !important;
                            margin-left: 0.15rem !important;
                            transition: all 0.15s ease !important;
                        }

                        /* Minimalist Border-Only Theming */
                        .bbpom-chip-all {
                            background-color: #ffffff !important;
                            color: #334155 !important;
                            border-color: #cbd5e1 !important;
                        }
                        .bbpom-chip-all .chip-count {
                            background-color: #f1f5f9 !important;
                            color: #475569 !important;
                        }
                        .bbpom-chip-all.is-active,
                        .bbpom-chip-all:hover {
                            background-color: #0f172a !important;
                            color: #ffffff !important;
                            border-color: #0f172a !important;
                            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.2) !important;
                        }
                        .bbpom-chip-all.is-active .chip-count,
                        .bbpom-chip-all:hover .chip-count {
                            background-color: rgba(255, 255, 255, 0.22) !important;
                            color: #ffffff !important;
                        }

                        .bbpom-chip-pending {
                            background-color: #ffffff !important;
                            color: #b45309 !important;
                            border-color: #fed7aa !important;
                        }
                        .bbpom-chip-pending .chip-dot {
                            background-color: #d97706 !important;
                        }
                        .bbpom-chip-pending .chip-count {
                            background-color: #fef3c7 !important;
                            color: #92400e !important;
                        }
                        .bbpom-chip-pending.is-active,
                        .bbpom-chip-pending:hover {
                            background-color: #d97706 !important;
                            color: #ffffff !important;
                            border-color: #b45309 !important;
                            box-shadow: 0 2px 4px rgba(217, 119, 6, 0.25) !important;
                        }
                        .bbpom-chip-pending.is-active .chip-dot,
                        .bbpom-chip-pending:hover .chip-dot {
                            background-color: #ffffff !important;
                        }
                        .bbpom-chip-pending.is-active .chip-count,
                        .bbpom-chip-pending:hover .chip-count {
                            background-color: rgba(255, 255, 255, 0.25) !important;
                            color: #ffffff !important;
                        }

                        .bbpom-chip-diproses {
                            background-color: #ffffff !important;
                            color: #1d4ed8 !important;
                            border-color: #bfdbfe !important;
                        }
                        .bbpom-chip-diproses .chip-dot {
                            background-color: #2563eb !important;
                        }
                        .bbpom-chip-diproses .chip-count {
                            background-color: #dbeafe !important;
                            color: #1e40af !important;
                        }
                        .bbpom-chip-diproses.is-active,
                        .bbpom-chip-diproses:hover {
                            background-color: #2563eb !important;
                            color: #ffffff !important;
                            border-color: #1d4ed8 !important;
                            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25) !important;
                        }
                        .bbpom-chip-diproses.is-active .chip-dot,
                        .bbpom-chip-diproses:hover .chip-dot {
                            background-color: #ffffff !important;
                        }
                        .bbpom-chip-diproses.is-active .chip-count,
                        .bbpom-chip-diproses:hover .chip-count {
                            background-color: rgba(255, 255, 255, 0.25) !important;
                            color: #ffffff !important;
                        }

                        .bbpom-chip-selesai {
                            background-color: #ffffff !important;
                            color: #047857 !important;
                            border-color: #a7f3d0 !important;
                        }
                        .bbpom-chip-selesai .chip-dot {
                            background-color: #059669 !important;
                        }
                        .bbpom-chip-selesai .chip-count {
                            background-color: #d1fae5 !important;
                            color: #065f46 !important;
                        }
                        .bbpom-chip-selesai.is-active,
                        .bbpom-chip-selesai:hover {
                            background-color: #059669 !important;
                            color: #ffffff !important;
                            border-color: #047857 !important;
                            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.25) !important;
                        }
                        .bbpom-chip-selesai.is-active .chip-dot,
                        .bbpom-chip-selesai:hover .chip-dot {
                            background-color: #ffffff !important;
                        }
                        .bbpom-chip-selesai.is-active .chip-count,
                        .bbpom-chip-selesai:hover .chip-count {
                            background-color: rgba(255, 255, 255, 0.25) !important;
                            color: #ffffff !important;
                        }

                        .bbpom-chip-ditolak {
                            background-color: #ffffff !important;
                            color: #be123c !important;
                            border-color: #fecdd3 !important;
                        }
                        .bbpom-chip-ditolak .chip-dot {
                            background-color: #e11d48 !important;
                        }
                        .bbpom-chip-ditolak .chip-count {
                            background-color: #ffe4e6 !important;
                            color: #9f1239 !important;
                        }
                        .bbpom-chip-ditolak.is-active,
                        .bbpom-chip-ditolak:hover {
                            background-color: #e11d48 !important;
                            color: #ffffff !important;
                            border-color: #be123c !important;
                            box-shadow: 0 2px 4px rgba(225, 29, 72, 0.25) !important;
                        }
                        .bbpom-chip-ditolak.is-active .chip-dot,
                        .bbpom-chip-ditolak:hover .chip-dot {
                            background-color: #ffffff !important;
                        }
                        .bbpom-chip-ditolak.is-active .chip-count,
                        .bbpom-chip-ditolak:hover .chip-count {
                            background-color: rgba(255, 255, 255, 0.25) !important;
                            color: #ffffff !important;
                        }

                        /* Alert Widget Mini Navbar Perfection */
                        .bbpom-alert-title-group {
                            display: inline-flex !important;
                            align-items: center !important;
                            gap: 0.5rem !important;
                            min-width: 0 !important;
                            flex-wrap: nowrap !important;
                        }
                        .bbpom-alert-icon-box {
                            display: inline-flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            width: 1.75rem !important;
                            height: 1.75rem !important;
                            border-radius: 0.5rem !important;
                            flex-shrink: 0 !important;
                            line-height: 1 !important;
                        }
                        .bbpom-alert-icon-box i {
                            font-size: 1rem !important;
                            line-height: 1 !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        }
                        .bbpom-alert-title-text {
                            font-weight: 700 !important;
                            font-size: 0.875rem !important;
                            color: #0f172a !important;
                            white-space: nowrap !important;
                            line-height: 1.25 !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                            margin: 0 !important;
                        }
                        .bbpom-alert-beacon {
                            display: inline-flex !important;
                            position: relative !important;
                            width: 0.5rem !important;
                            height: 0.5rem !important;
                            flex-shrink: 0 !important;
                        }
                        .bbpom-alert-beacon-dot {
                            width: 0.5rem !important;
                            height: 0.5rem !important;
                            border-radius: 9999px !important;
                            background-color: #e11d48 !important;
                        }
                        .bbpom-alert-badge {
                            display: inline-flex !important;
                            align-items: center !important;
                            gap: 0.3rem !important;
                            padding: 0.22rem 0.55rem !important;
                            border-radius: 9999px !important;
                            font-size: 0.6875rem !important;
                            font-weight: 700 !important;
                            line-height: 1 !important;
                            white-space: nowrap !important;
                            flex-shrink: 0 !important;
                        }

                        /* Table Tooltip Light Grey Theme */
                        .tippy-box,
                        [data-tippy-root] .tippy-box {
                            font-size: 0.75rem !important;
                            line-height: 1.35 !important;
                            max-width: 24rem !important;
                            border-radius: 0.5rem !important;
                            background-color: #f1f5f9 !important;
                            color: #0f172a !important;
                            border: 1px solid #cbd5e1 !important;
                            padding: 0.35rem 0.6rem !important;
                            box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.12), 0 2px 4px -2px rgba(15, 23, 42, 0.06) !important;
                        }
                        .tippy-content,
                        [data-tippy-root] .tippy-content {
                            font-size: 0.75rem !important;
                            font-weight: 500 !important;
                            color: #0f172a !important;
                            padding: 0 !important;
                        }
                        .tippy-box > .tippy-arrow {
                            color: #f1f5f9 !important;
                        }
                        .tippy-box > .tippy-arrow::before {
                            color: #f1f5f9 !important;
                        }

                        /* BBPOM Primary Header Action Buttons (Hijau Gelap BPOM & Teks Putih Terang) */
                        .bbpom-header-create-btn,
                        .fi-page-header .fi-btn-color-primary,
                        .fi-page-header-actions .fi-btn-color-primary,
                        .fi-header-actions .fi-btn-color-primary,
                        .fi-page-header a.fi-btn-color-primary,
                        .fi-page-header button.fi-btn-color-primary {
                            background: linear-gradient(135deg, #065f46 0%, #064e3b 60%, #022c22 100%) !important;
                            color: #ffffff !important;
                            border: 1px solid rgba(52, 211, 153, 0.4) !important;
                            border-radius: 0.625rem !important;
                            font-weight: 600 !important;
                            font-size: 0.8125rem !important;
                            padding: 0.5rem 1.1rem !important;
                            box-shadow: 0 4px 12px -2px rgba(6, 78, 59, 0.4), 0 2px 4px -2px rgba(6, 78, 59, 0.2) !important;
                            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                            display: inline-flex !important;
                            align-items: center !important;
                            gap: 0.45rem !important;
                        }
                        .bbpom-header-create-btn:hover,
                        .fi-page-header .fi-btn-color-primary:hover,
                        .fi-page-header-actions .fi-btn-color-primary:hover,
                        .fi-header-actions .fi-btn-color-primary:hover,
                        .fi-page-header a.fi-btn-color-primary:hover,
                        .fi-page-header button.fi-btn-color-primary:hover {
                            background: linear-gradient(135deg, #047857 0%, #065f46 60%, #064e3b 100%) !important;
                            color: #ffffff !important;
                            border-color: rgba(110, 231, 183, 0.7) !important;
                            transform: translateY(-1px) !important;
                            box-shadow: 0 6px 18px -2px rgba(6, 95, 70, 0.5), 0 3px 6px -2px rgba(6, 95, 70, 0.25) !important;
                        }
                        .bbpom-header-create-btn:active,
                        .fi-page-header .fi-btn-color-primary:active,
                        .fi-page-header-actions .fi-btn-color-primary:active,
                        .fi-header-actions .fi-btn-color-primary:active,
                        .fi-page-header a.fi-btn-color-primary:active,
                        .fi-page-header button.fi-btn-color-primary:active {
                            transform: translateY(0) !important;
                            box-shadow: 0 2px 6px -1px rgba(6, 78, 59, 0.3) !important;
                        }
                        .bbpom-header-create-btn .fi-btn-label,
                        .bbpom-header-create-btn .fi-btn-icon,
                        .bbpom-header-create-btn svg,
                        .bbpom-header-create-btn span,
                        .fi-page-header .fi-btn-color-primary .fi-btn-label,
                        .fi-page-header .fi-btn-color-primary .fi-btn-icon,
                        .fi-page-header .fi-btn-color-primary svg,
                        .fi-page-header-actions .fi-btn-color-primary .fi-btn-label,
                        .fi-page-header-actions .fi-btn-color-primary .fi-btn-icon,
                        .fi-page-header-actions .fi-btn-color-primary svg {
                            color: #ffffff !important;
                            stroke: #ffffff !important;
                        }

                        /* Edit Ticket: Unified Master Dossier Card & Hero Gradient Theming */
                        .ticket-left-column {
                            display: flex !important;
                            flex-direction: column !important;
                            height: 100% !important;
                            gap: 1.25rem !important;
                        }
                        .ticket-right-column {
                            display: flex !important;
                            flex-direction: column !important;
                            height: 100% !important;
                        }
                        .ticket-left-column .fi-section,
                        .ticket-right-column .fi-section,
                        .ticket-form-card-full,
                        .ticket-master-card,
                        .ticket-master-card .fi-section {
                            border-color: rgba(51, 65, 85, 0.25) !important;
                            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.06), 0 2px 4px -2px rgba(15, 23, 42, 0.04) !important;
                            background-color: #ffffff !important;
                            border-radius: 1rem !important;
                            overflow: visible !important;
                        }
                        /* Unified Slate-to-Emerald Hero Gradient Header (Matching Homepage Welcome Banner) */
                        .ticket-left-column .fi-section-header,
                        .ticket-right-column .fi-section-header,
                        .ticket-master-card .fi-section-header {
                            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #022c22 100%) !important;
                            border-bottom: 1px solid rgba(51, 65, 85, 0.6) !important;
                            border-top-left-radius: 1rem !important;
                            border-top-right-radius: 1rem !important;
                            padding: 0.85rem 1.25rem !important;
                        }
                        .ticket-left-column .fi-section-header-heading,
                        .ticket-left-column .fi-section-header-heading *,
                        .ticket-right-column .fi-section-header-heading,
                        .ticket-right-column .fi-section-header-heading *,
                        .ticket-master-card .fi-section-header-heading,
                        .ticket-master-card .fi-section-header-heading * {
                            color: #ffffff !important;
                            font-weight: 700 !important;
                            font-size: 0.95rem !important;
                            letter-spacing: -0.01em !important;
                        }
                        .ticket-left-column .fi-section-header-description,
                        .ticket-left-column .fi-section-header-description *,
                        .ticket-right-column .fi-section-header-description,
                        .ticket-right-column .fi-section-header-description *,
                        .ticket-master-card .fi-section-header-description,
                        .ticket-master-card .fi-section-header-description * {
                            color: #a7f3d0 !important;
                            font-size: 0.8125rem !important;
                        }

                        /* Dropdown Styling for Custom Select (Clean, Non-Clipping, Downward Oriented) */
                        .fi-dropdown-panel {
                            z-index: 9999 !important;
                            background-color: #ffffff !important;
                            border-radius: 0.75rem !important;
                            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1) !important;
                            border: 1px solid rgba(203, 213, 225, 0.9) !important;
                            padding: 0.35rem !important;
                        }
                        .fi-dropdown-list {
                            display: flex !important;
                            flex-direction: column !important;
                            gap: 0.2rem !important;
                            list-style: none !important;
                            margin: 0 !important;
                            padding: 0 !important;
                        }
                        .fi-dropdown-list-item.fi-select-input-option {
                            padding: 0.6rem 0.85rem !important;
                            border-radius: 0.5rem !important;
                            font-size: 0.8125rem !important;
                            font-weight: 500 !important;
                            color: #1e293b !important;
                            cursor: pointer !important;
                            transition: all 0.15s ease-in-out !important;
                            display: flex !important;
                            align-items: center !important;
                        }
                        .fi-dropdown-list-item.fi-select-input-option:hover,
                        .fi-dropdown-list-item.fi-select-input-option.is-highlighted,
                        .fi-dropdown-list-item.fi-select-input-option[aria-selected="true"] {
                            background-color: #ecfdf5 !important;
                            color: #065f46 !important;
                            font-weight: 600 !important;
                        }

                        /* Actions container strictly left-aligned at bottom of form */
                        .ticket-form-bottom-actions,
                        .ticket-master-card .ticket-form-bottom-actions,
                        .ticket-master-card .fi-sc-actions,
                        .ticket-left-actions {
                            width: 100% !important;
                            max-width: 100% !important;
                            display: flex !important;
                            justify-content: flex-start !important;
                            align-items: center !important;
                            text-align: left !important;
                            margin-left: 0 !important;
                            margin-right: auto !important;
                            margin-inline: 0 auto !important;
                            gap: 0.75rem !important;
                            padding-top: 1.25rem !important;
                            padding-bottom: 0.5rem !important;
                            border-top: 1px solid rgba(226, 232, 240, 0.8) !important;
                        }
                        .ticket-form-bottom-actions .fi-ac,
                        .ticket-master-card .fi-sc-actions .fi-ac,
                        .ticket-left-actions .fi-ac {
                            display: flex !important;
                            justify-content: flex-start !important;
                            align-items: center !important;
                            margin-left: 0 !important;
                            margin-right: auto !important;
                            margin-inline: 0 auto !important;
                            gap: 0.75rem !important;
                            width: auto !important;
                        }
                        .ticket-form-bottom-actions .fi-ac > *,
                        .ticket-master-card .fi-sc-actions .fi-ac > * {
                            margin-left: 0 !important;
                            margin-right: 0 !important;
                        }
                        .ticket-master-card .fi-grid-col:has(.ticket-form-bottom-actions),
                        .ticket-master-card .fi-grid-col:has(.fi-sc-actions) {
                            width: 100% !important;
                            justify-items: start !important;
                        }
                        .ticket-master-card .fi-grid-col:has(.ticket-form-bottom-actions) > .fi-sc-component,
                        .ticket-master-card .fi-grid-col:has(.fi-sc-actions) > .fi-sc-component {
                            width: 100% !important;
                            display: flex !important;
                            justify-content: flex-start !important;
                        }

                        .ticket-right-column > .fi-section {
                            height: 100% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .ticket-right-column > .fi-section > .fi-section-content-ctn {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                        }
                        .ticket-right-column > .fi-section > .fi-section-content-ctn > .fi-section-content {
                            flex: 1 1 0% !important;
                            display: flex !important;
                            flex-direction: column !important;
                            justify-content: space-between !important;
                        }

                        /* BBPOM Export Modal: Format Buttons (Side-by-side & Distinct Colors) */
                        .bbpom-export-formats-container {
                            display: grid !important;
                            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                            gap: 0.75rem !important;
                            width: 100% !important;
                        }
                        @media (max-width: 640px) {
                            .bbpom-export-formats-container {
                                grid-template-columns: 1fr !important;
                            }
                        }
                        .bbpom-format-btn {
                            position: relative !important;
                            display: flex !important;
                            align-items: center !important;
                            gap: 0.85rem !important;
                            padding: 0.75rem 1rem !important;
                            border-radius: 0.75rem !important;
                            border-width: 2px !important;
                            border-style: solid !important;
                            text-align: left !important;
                            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                            cursor: pointer !important;
                            background-color: #ffffff !important;
                            user-select: none !important;
                            outline: none !important;
                            width: 100% !important;
                            box-sizing: border-box !important;
                        }
                        .bbpom-format-btn .text-content {
                            min-width: 0 !important;
                            flex: 1 1 0% !important;
                        }
                        .bbpom-format-title {
                            font-size: 0.875rem !important;
                            font-weight: 700 !important;
                            line-height: 1.25 !important;
                        }
                        .bbpom-format-desc {
                            font-size: 0.72rem !important;
                            color: #64748b !important;
                            margin-top: 0.18rem !important;
                            line-height: 1.25 !important;
                        }
                        /* PDF Format (Merah / Rose) */
                        .bbpom-format-btn.btn-pdf {
                            border-color: #fecdd3 !important;
                            background-color: #ffffff !important;
                        }
                        .bbpom-format-btn.btn-pdf .bbpom-format-title {
                            color: #9f1239 !important;
                        }
                        .bbpom-format-btn.btn-pdf .icon-box {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            width: 2.5rem !important;
                            height: 2.5rem !important;
                            border-radius: 0.5rem !important;
                            background-color: #ffe4e6 !important;
                            color: #e11d48 !important;
                            font-size: 1.4rem !important;
                            flex-shrink: 0 !important;
                            transition: all 0.2s ease !important;
                        }
                        .bbpom-format-btn.btn-pdf:hover {
                            border-color: #f43f5e !important;
                            background-color: #fff1f2 !important;
                            transform: translateY(-1px) !important;
                            box-shadow: 0 4px 14px -2px rgba(225, 29, 72, 0.18) !important;
                        }
                        .bbpom-format-btn.btn-pdf:hover .icon-box {
                            background-color: #f43f5e !important;
                            color: #ffffff !important;
                        }
                        .bbpom-format-btn.btn-pdf.is-active {
                            border-color: #e11d48 !important;
                            background-color: #fff1f2 !important;
                            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.22), 0 4px 12px -2px rgba(225, 29, 72, 0.18) !important;
                        }
                        .bbpom-format-btn.btn-pdf.is-active .icon-box {
                            background-color: #e11d48 !important;
                            color: #ffffff !important;
                            box-shadow: 0 2px 6px -1px rgba(225, 29, 72, 0.35) !important;
                        }
                        .bbpom-format-btn.btn-pdf.is-active .bbpom-radio-indicator {
                            border-color: #e11d48 !important;
                            background-color: #e11d48 !important;
                        }
                        /* Excel Format (Hijau / Emerald) */
                        .bbpom-format-btn.btn-excel {
                            border-color: #a7f3d0 !important;
                            background-color: #ffffff !important;
                        }
                        .bbpom-format-btn.btn-excel .bbpom-format-title {
                            color: #064e3b !important;
                        }
                        .bbpom-format-btn.btn-excel .icon-box {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            width: 2.5rem !important;
                            height: 2.5rem !important;
                            border-radius: 0.5rem !important;
                            background-color: #d1fae5 !important;
                            color: #059669 !important;
                            font-size: 1.4rem !important;
                            flex-shrink: 0 !important;
                            transition: all 0.2s ease !important;
                        }
                        .bbpom-format-btn.btn-excel:hover {
                            border-color: #10b981 !important;
                            background-color: #ecfdf5 !important;
                            transform: translateY(-1px) !important;
                            box-shadow: 0 4px 14px -2px rgba(5, 150, 105, 0.18) !important;
                        }
                        .bbpom-format-btn.btn-excel:hover .icon-box {
                            background-color: #10b981 !important;
                            color: #ffffff !important;
                        }
                        .bbpom-format-btn.btn-excel.is-active {
                            border-color: #059669 !important;
                            background-color: #ecfdf5 !important;
                            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.22), 0 4px 12px -2px rgba(5, 150, 105, 0.18) !important;
                        }
                        .bbpom-format-btn.btn-excel.is-active .icon-box {
                            background-color: #059669 !important;
                            color: #ffffff !important;
                            box-shadow: 0 2px 6px -1px rgba(5, 150, 105, 0.35) !important;
                        }
                        .bbpom-format-btn.btn-excel.is-active .bbpom-radio-indicator {
                            border-color: #059669 !important;
                            background-color: #059669 !important;
                        }
                        .bbpom-radio-indicator {
                            width: 1.25rem !important;
                            height: 1.25rem !important;
                            border-radius: 9999px !important;
                            border: 2px solid #cbd5e1 !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            margin-left: auto !important;
                            flex-shrink: 0 !important;
                            transition: all 0.2s ease !important;
                            background-color: #ffffff !important;
                        }
                        .bbpom-radio-dot {
                            width: 0.45rem !important;
                            height: 0.45rem !important;
                            border-radius: 9999px !important;
                            background-color: #ffffff !important;
                            display: inline-block !important;
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
