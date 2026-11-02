@props(['ticket'])

@php
    $survey = $ticket->satisfactionSurvey;
@endphp

@if($survey)
    <div class="bg-white rounded-2xl border border-emerald-200/90 shadow-xs overflow-hidden">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-gradient-to-r from-emerald-50 via-teal-50/50 to-white border-b border-emerald-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-2xs">
                    <i class="ri-survey-line"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Ulasan &amp; Penilaian Layanan</h4>
                    <p class="text-[10px] text-slate-500">Dikirim pada {{ $survey->created_at->format('d/m/Y H:i') }} WIB</p>
                </div>
            </div>

            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-900">
                <i class="ri-star-fill text-amber-500 text-xs"></i>
                <span class="font-black text-xs">{{ $survey->formatted_score }}</span>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <!-- 4 Criteria Score Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                    <p class="text-[10px] text-slate-500 font-medium">Kecepatan</p>
                    <div class="flex items-center justify-center gap-1 mt-1 text-amber-500">
                        <i class="ri-star-fill text-xs"></i>
                        <span class="text-xs font-extrabold text-slate-900">{{ $survey->speed_rating }}/5</span>
                    </div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                    <p class="text-[10px] text-slate-500 font-medium">Kualitas Solusi</p>
                    <div class="flex items-center justify-center gap-1 mt-1 text-amber-500">
                        <i class="ri-star-fill text-xs"></i>
                        <span class="text-xs font-extrabold text-slate-900">{{ $survey->quality_rating }}/5</span>
                    </div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                    <p class="text-[10px] text-slate-500 font-medium">Komunikasi</p>
                    <div class="flex items-center justify-center gap-1 mt-1 text-amber-500">
                        <i class="ri-star-fill text-xs"></i>
                        <span class="text-xs font-extrabold text-slate-900">{{ $survey->communication_rating }}/5</span>
                    </div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                    <p class="text-[10px] text-slate-500 font-medium">Sistem ELITE</p>
                    <div class="flex items-center justify-center gap-1 mt-1 text-amber-500">
                        <i class="ri-star-fill text-xs"></i>
                        <span class="text-xs font-extrabold text-slate-900">{{ $survey->system_rating }}/5</span>
                    </div>
                </div>
            </div>

            <!-- Feedback Quote (if any) -->
            @if($survey->feedback)
                <div class="p-3.5 rounded-xl bg-emerald-50/50 border border-emerald-100/80 flex items-start gap-2.5">
                    <i class="ri-double-quotes-l text-emerald-600 text-base shrink-0 mt-0.5"></i>
                    <div class="text-xs text-slate-700 leading-relaxed italic">
                        "{{ $survey->feedback }}"
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
