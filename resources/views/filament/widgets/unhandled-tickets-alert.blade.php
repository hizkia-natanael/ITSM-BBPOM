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
            <span style="display: inline-flex; align-items: center; padding: 0.22rem 0.65rem; border-radius: 9999px; font-size: 0.7125rem; font-weight: 600; background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0; white-space: nowrap;">
                {{ $unhandledCount }} yang belum ditangani
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
