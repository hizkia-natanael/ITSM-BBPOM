<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
                        </li>
                        <li>
                            <span class="mx-1 text-gray-400">/</span>
                        </li>
                        <li class="font-semibold text-emerald-800">Daftar Pengaduan</li>
                    </ol>
                </nav>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    Riwayat & Status Pengaduan Saya
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Daftar seluruh tiket layanan TI yang telah Anda ajukan ke Helpdesk BBPOM Semarang
                </p>
            </div>
            <div>
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Pengaduan Baru
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
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}" 
                                placeholder="Cari nomor tiket, subjek, atau kata kunci..." 
                                class="pl-9 block w-full rounded-lg border-gray-300 text-xs text-gray-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                            />
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <select 
                            name="category_id" 
                            class="block w-full rounded-lg border-gray-300 text-xs text-gray-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
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
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            Terapkan
                        </button>
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            <a href="{{ route('tickets.index') }}" class="inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-100 transition" title="Reset filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Pengaduan -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-600 font-semibold border-b border-gray-100 uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="py-3.5 px-4 sm:px-6">No. Tiket</th>
                                <th scope="col" class="py-3.5 px-4">Kategori</th>
                                <th scope="col" class="py-3.5 px-4">Subjek / Masalah</th>
                                <th scope="col" class="py-3.5 px-4">Status</th>
                                <th scope="col" class="py-3.5 px-4">Tanggapan</th>
                                <th scope="col" class="py-3.5 px-4">Tanggal Pengajuan</th>
                                <th scope="col" class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-4 px-4 sm:px-6 font-bold text-gray-900 whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-emerald-700 hover:underline flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                            {{ $ticket->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-900 font-medium max-w-xs sm:max-w-sm truncate">
                                        {{ $ticket->subject }}
                                        @if($ticket->attachment)
                                            <span class="inline-flex items-center gap-1 text-[10px] text-gray-400 ms-1.5" title="Memiliki lampiran">
                                                <i class="ri-attachment-2 text-xs"></i>
                                                <span>Lampiran</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <x-ticket-status-badge :status="$ticket->status" />
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-gray-500">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-slate-100 text-slate-700 font-semibold">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            {{ $ticket->responses->count() }} pesan
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-gray-500">
                                        {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                                            Lihat & Tracking
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($tickets->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @else
                <div class="py-16 px-4 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm">Tidak Ditemukan Pengaduan</h4>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            Tidak ada tiket yang sesuai dengan filter atau kata kunci yang Anda masukkan. Coba sesuaikan kata kunci pencarian.
                        @else
                            Anda belum memiliki riwayat pengaduan kendala TI.
                        @endif
                    </p>
                    <div class="mt-4 flex justify-center gap-2">
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                Hapus Filter
                            </a>
                        @endif
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Pengaduan Baru
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
