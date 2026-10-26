@props(['recentTickets'])

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-5 sm:px-6 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="ri-history-line text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pengaduan Terbaru Anda</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                        {{ $recentTickets->count() }} Terkini
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Daftar 5 tiket pengaduan terakhir yang Anda ajukan</p>
            </div>
        </div>
        <div>
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 hover:text-emerald-700 bg-slate-50 hover:bg-emerald-50/70 border border-slate-200 hover:border-emerald-300 transition duration-150">
                <span>Lihat Semua Riwayat</span>
                <i class="ri-arrow-right-line text-xs"></i>
            </a>
        </div>
    </div>

    @if($recentTickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 text-slate-500 font-semibold border-b border-slate-200/80 text-xs">
                    <tr>
                        <th scope="col" class="py-2.5 px-4 sm:px-5 font-semibold">No. Tiket</th>
                        <th scope="col" class="py-2.5 px-4 font-semibold">Kategori</th>
                        <th scope="col" class="py-2.5 px-4 font-semibold">Subjek Kendala</th>
                        <th scope="col" class="py-2.5 px-4 font-semibold">Status Progres</th>
                        <th scope="col" class="py-2.5 px-4 font-semibold">Waktu Pengajuan</th>
                        <th scope="col" class="py-2.5 px-4 sm:px-5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentTickets as $ticket)
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
    @else
        <div class="py-12 px-4 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center mb-3">
                <i class="ri-inbox-archive-line text-2xl"></i>
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Belum Ada Pengaduan</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                Anda belum pernah membuat tiket pengaduan kendala TI. Jika membutuhkan bantuan teknisi, silakan klik tombol di bawah.
            </p>
            <div class="mt-4">
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-semibold shadow-sm transition">
                    <i class="ri-add-line text-base"></i>
                    <span>Buat Pengaduan Pertama Anda</span>
                </a>
            </div>
        </div>
    @endif
</div>
