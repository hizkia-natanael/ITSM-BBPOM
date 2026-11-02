@php
    $unhandledTickets = $this->getUnhandledTickets();
    $unhandledCount = $this->getUnhandledCount();
@endphp

<x-filament-widgets::widget class="fi-wi-unhandled-tickets-alert" style="height: 100%;">
    <x-filament::section class="h-full flex flex-col justify-between">
        <x-slot name="heading">
            <span style="font-weight: 700; font-size: 0.925rem; color: #0f172a; white-space: nowrap; line-height: 1.25;">
                Aduan Belum Ditangani
            </span>
        </x-slot>

        <x-slot name="afterHeader">
            <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.22rem 0.65rem; border-radius: 9999px; font-size: 0.7125rem; font-weight: 700; {{ $unhandledCount > 0 ? 'background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;' : 'background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;' }}; white-space: nowrap;">
                <span style="display: inline-block; width: 0.4rem; height: 0.4rem; border-radius: 9999px; {{ $unhandledCount > 0 ? 'background-color: #d97706;' : 'background-color: #059669;' }}"></span>
                <span>{{ $unhandledCount }} Belum Ditangani</span>
            </span>
        </x-slot>

        <div wire:poll.15s style="flex: 1 1 0%; display: flex; flex-direction: column; justify-content: space-between; min-height: 0;">
            <x-unhandled-ticket-alert
                :unhandled-tickets="$unhandledTickets"
                :unhandled-count="$unhandledCount"
            />
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
