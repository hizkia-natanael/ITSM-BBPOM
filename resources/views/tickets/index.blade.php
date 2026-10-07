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
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <form method="GET" action="{{ route('tickets.index') }}" class="space-y-4">
                <!-- Status Filter Pills -->
                <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 pb-4">
                    <span class="text-xs font-semibold text-gray-600 me-2">Status:</span>
                    <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']))) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ empty($selectedStatus) ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua Status
                    </a>
                    <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'pending'])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'pending' ? 'bg-amber-500 text-white font-semibold shadow-xs' : 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100' }}">
                        Menunggu (Pending)
                    </a>
                    <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'diproses'])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'diproses' ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100' }}">
                        Sedang Diproses
                    </a>
                    <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'selesai'])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'selesai' ? 'bg-emerald-600 text-white font-semibold shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' }}">
                        Selesai
                    </a>
                    <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'ditolak'])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'ditolak' ? 'bg-rose-600 text-white font-semibold shadow-xs' : 'bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100' }}">
                        Ditolak
                    </a>
                </div>

                <!-- Search & Category Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                    <div class="sm:col-span-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="ri-search-line text-xs text-slate-400"></i>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}" 
                                placeholder="Cari nomor tiket, subjek, atau kata kunci..." 
                                class="pl-8.5 block w-full rounded-xl border-slate-200 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs py-2"
                            />
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <select 
                            name="category_id" 
                            class="block w-full rounded-xl border-slate-200 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs py-2"
                        >
                            <option value="">Semua Kategori Layanan</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex gap-2">
                        @if(request()->filled('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-2xs transition">
                            Terapkan
                        </button>
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            <a href="{{ route('tickets.index') }}" class="inline-flex justify-center items-center px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 transition" title="Reset filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Pengaduan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/75 text-slate-500 font-semibold border-b border-slate-200/80 text-xs">
                            <tr>
                                <th scope="col" class="py-2.5 px-4 sm:px-5 font-semibold">No. Tiket</th>
                                <th scope="col" class="py-2.5 px-4 font-semibold">Kategori</th>
                                <th scope="col" class="py-2.5 px-4 font-semibold">Subjek Kendala</th>
                                <th scope="col" class="py-2.5 px-4 font-semibold">Status Progres</th>
                                <th scope="col" class="py-2.5 px-4 font-semibold">Tanggapan</th>
                                <th scope="col" class="py-2.5 px-4 font-semibold">Waktu Pengajuan</th>
                                <th scope="col" class="py-2.5 px-4 sm:px-5 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-slate-50/70 transition-colors group">
                                    <td class="py-3 px-4 sm:px-5 whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100/90 group-hover:bg-emerald-50 border border-slate-200/80 group-hover:border-emerald-300 transition shadow-2xs" title="Lihat detail tiket {{ $ticket->ticket_number }}">
                                            <i class="ri-hashtag text-[10px] text-slate-400 group-hover:text-emerald-600 transition-colors"></i>
                                            <span class="font-mono font-bold text-[11px] text-slate-900 group-hover:text-emerald-800">
                                                {{ $ticket->ticket_number }}
                                            </span>
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <x-ticket-category-badge :ticket="$ticket" :category="$ticket->category" />
                                    </td>
                                    <td class="py-3 px-4 max-w-xs sm:max-w-sm md:max-w-md">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-sm font-semibold text-slate-900 hover:text-emerald-700 transition truncate" title="{{ $ticket->subject }}">
                                                {{ $ticket->subject }}
                                            </a>
                                            @if($ticket->attachment)
                                                <span class="inline-flex items-center gap-0.5 text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded shrink-0 border border-slate-200/60" title="Memiliki lampiran berkas">
                                                    <i class="ri-attachment-2 text-xs text-slate-500"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <x-ticket-status-badge :status="$ticket->status" :handler="$ticket->handledBy" :compact="true" />
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60">
                                            <i class="ri-chat-3-line text-xs text-slate-400"></i>
                                            <span>{{ $ticket->responses->count() }} pesan</span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-xs text-slate-500">
                                        {{ $ticket->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </td>
                                    <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-emerald-600 text-slate-700 hover:text-white border border-slate-200 hover:border-emerald-600 shadow-2xs hover:shadow-xs transition-all duration-150">
                                            <span>Detail</span>
                                            <i class="ri-arrow-right-line text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Record Count Bar -->
                @if($tickets->hasPages())
                    <div class="px-5 py-3.5 border-t border-slate-100">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="px-5 py-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-600">
                        <div class="flex flex-wrap items-center gap-3">
                            <p class="text-sm font-medium text-gray-700 leading-5">
                                Showing {{ $tickets->firstItem() ?? 1 }} to {{ $tickets->lastItem() ?? $tickets->count() }} of {{ $tickets->total() }} results
                            </p>

                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                <span class="text-gray-300">|</span>
                                <label for="pagination_per_page_single" class="text-sm font-medium text-gray-700 whitespace-nowrap">Tampilkan:</label>
                                <select
                                    id="pagination_per_page_single"
                                    name="per_page"
                                    class="text-xs py-1 px-2.5 rounded-lg border-gray-300 bg-white text-gray-700 font-medium focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs cursor-pointer"
                                    onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.set('page', '1'); window.location.href = u.toString();"
                                >
                                    @foreach([10, 25, 50, 100] as $count)
                                        <option value="{{ $count }}" {{ (int)request('per_page', 10) === $count ? 'selected' : '' }}>
                                            {{ $count }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-sm font-medium text-gray-700 whitespace-nowrap">per halaman</span>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="py-16 px-4 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center mb-3">
                        <i class="ri-inbox-archive-line text-3xl"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">Tidak Ditemukan Pengaduan</h4>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mt-1">
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            Tidak ada tiket yang sesuai dengan filter atau kata kunci yang Anda masukkan. Coba sesuaikan kata kunci pencarian.
                        @else
                            Anda belum memiliki riwayat pengaduan kendala TI.
                        @endif
                    </p>
                    <div class="mt-4 flex justify-center gap-2">
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Hapus Filter
                            </a>
                        @endif
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                            <i class="ri-add-line text-base"></i>
                            <span>Buat Pengaduan Baru</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
