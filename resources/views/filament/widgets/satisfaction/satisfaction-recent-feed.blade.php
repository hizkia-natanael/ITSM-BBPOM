@props(['surveys'])

<div class="h-full flex flex-col justify-between space-y-3">
    @if($surveys->isEmpty())
        <div class="h-full min-h-[200px] flex flex-col items-center justify-center p-6 text-center rounded-2xl bg-slate-50/60 border border-slate-200/80">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-2.5 shadow-2xs">
                <i class="ri-chat-smile-2-line"></i>
            </div>
            <h4 class="text-xs font-bold text-slate-800">Belum Ada Ulasan Masuk</h4>
            <p class="text-[11px] text-slate-500 max-w-sm mt-1">
                Ulasan kepuasan dari pegawai pemohon yang tiketnya telah diselesaikan akan otomatis muncul secara live di sini.
            </p>
        </div>
    @else
        <div class="space-y-2.5 max-h-[340px] overflow-y-auto pr-1">
            @foreach($surveys as $survey)
                <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-emerald-300 shadow-2xs transition-colors space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-600 to-teal-700 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($survey->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h5 class="text-xs font-bold text-slate-900 truncate">
                                    {{ $survey->user->name ?? 'Pegawai' }}
                                </h5>
                                <a
                                    href="{{ \App\Filament\Resources\Tickets\TicketResource::getUrl('edit', ['record' => $survey->ticket_id]) }}"
                                    class="text-[10px] font-mono font-medium text-emerald-700 hover:underline"
                                >
                                    {{ $survey->ticket->ticket_number ?? '#TIK' }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 border border-amber-200 text-amber-900 text-[11px] font-black">
                                <i class="ri-star-fill text-amber-500 text-[10px]"></i>
                                {{ number_format($survey->average_rating, 1) }}
                            </span>
                            <span class="text-[10px] text-slate-400">
                                {{ $survey->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    @if($survey->feedback)
                        <div class="p-2 rounded-lg bg-slate-50 text-[11px] text-slate-700 italic border-l-2 border-emerald-500 line-clamp-2">
                            "{{ $survey->feedback }}"
                        </div>
                    @endif

                    <div class="flex items-center gap-2 text-[10px] text-slate-500 pt-0.5">
                        <span>Kecepatan: <strong class="text-slate-800">{{ $survey->speed_rating }}/5</strong></span>
                        <span>&bull;</span>
                        <span>Kualitas: <strong class="text-slate-800">{{ $survey->quality_rating }}/5</strong></span>
                        <span>&bull;</span>
                        <span>Komunikasi: <strong class="text-slate-800">{{ $survey->communication_rating }}/5</strong></span>
                        <span>&bull;</span>
                        <span>Sistem: <strong class="text-slate-800">{{ $survey->system_rating }}/5</strong></span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
