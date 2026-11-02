<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'icon' => 'ri-home-4-line']
            ]" />
            <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                Dashboard Layanan TI Pegawai
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                Balai Besar Pengawas Obat dan Makanan di Semarang
            </p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-dashboard.hero-banner />
        <x-dashboard.stats-grid :stats="$stats" />
        <x-dashboard.recent-tickets-table :recentTickets="$recentTickets" />
    </div>
</x-app-layout>
