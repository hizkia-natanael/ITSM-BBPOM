<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('dashboard')],
                    ['label' => 'Daftar Pengaduan']
                ]" />
                <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Riwayat & Status Pengaduan Saya
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Daftar seluruh tiket pengaduan layanan TI yang telah Anda ajukan ke Helpdesk BBPOM di Semarang
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white text-xs font-bold rounded-xl shadow-xs hover:shadow-md transition-all">
                    <i class="ri-add-circle-line text-base"></i>
                    <span>Buat Pengaduan Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter & Search Toolbar -->
        <x-tickets.filter-bar 
            :categories="$categories" 
            :selected-status="$selectedStatus" 
            :selected-category="$selectedCategory" 
            :search="$search" 
        />

        <!-- Tabel Daftar Pengaduan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            @if($tickets->count() > 0)
                <x-tickets.table-list :tickets="$tickets" />

                <!-- Pagination & Record Count Bar -->
                @if($tickets->hasPages())
                    <div class="px-5 py-3.5 border-t border-slate-100">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="px-5 py-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs sm:text-sm text-gray-600">
                        <div class="flex flex-wrap items-center gap-3">
                            <p class="text-sm font-medium text-gray-700 leading-5">
                                Showing {{ $tickets->firstItem() ?? 1 }} to {{ $tickets->lastItem() ?? $tickets->count() }} of {{ $tickets->total() }} results
                            </p>

                            <x-per-page-selector id="pagination_per_page_single" />
                        </div>
                    </div>
                @endif
            @else
                <x-tickets.empty-state />
            @endif
        </div>
    </div>
</x-app-layout>
