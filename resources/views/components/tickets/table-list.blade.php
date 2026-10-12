@props([
    'tickets',
])

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
