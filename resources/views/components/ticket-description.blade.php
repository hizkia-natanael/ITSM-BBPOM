@props(['description'])

@php
    $extracted = \App\Services\Tickets\TicketDynamicPayloadCompiler::extractStructuredPayload($description);
    $isStructured = $extracted['is_structured'];
    $meta = $extracted['meta'];
    $fields = $extracted['fields'];
    $longFields = $extracted['long_fields'];

    $getFieldIcon = fn(string $key): string => \App\Services\Tickets\TicketDynamicPayloadCompiler::getFieldIcon($key);
@endphp

@if($isStructured)
    <div class="space-y-5">
        <!-- Metadata Header Formulir (Tanggal & Pemohon) -->
        @if(isset($meta['Tanggal Pengajuan']) || isset($meta['Nama Pemohon']))
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                @if(isset($meta['Tanggal Pengajuan']))
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                            {{ $meta['Tanggal Pengajuan'] }} WIB
                        </span>
                    </div>
                @endif

                @if(isset($meta['Nama Pemohon']))
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[11px] font-medium">Nama Pemohon:</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                            {{ $meta['Nama Pemohon'] }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <!-- Grid Rincian Formulir Spesifikasi -->
        @if(!empty($fields))
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
                    <i class="ri-list-check-3 text-emerald-700 text-sm"></i>
                    Rincian Kebutuhan & Parameter Formulir:
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($fields as $label => $val)
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-colors">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="{{ $getFieldIcon($label) }} text-slate-400 text-sm"></i>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 truncate" title="{{ $label }}">
                                    {{ $label }}
                                </span>
                            </div>
                            <div class="text-xs font-semibold text-slate-900 break-words font-mono bg-slate-50/80 px-2.5 py-1.5 rounded-lg border border-slate-100">
                                {{ $val ?: '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Long Text Fields (Catatan Tambahan, Deskripsi Masalah, dll) -->
        @if(!empty($longFields))
            <div class="space-y-3 pt-2">
                @foreach($longFields as $longLabel => $longVal)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mb-2">
                            <i class="{{ (str_contains(strtolower($longLabel), 'materi') || str_contains(strtolower($longLabel), 'infografis') || str_contains(strtolower($longLabel), 'gambar')) ? 'ri-image-edit-line' : 'ri-file-text-line' }} text-emerald-700"></i>
                            {{ $longLabel }}:
                        </span>
                        <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-white p-3 rounded-lg border border-slate-200/60">{{ trim($longVal) }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@else
    <!-- Tampilan Plain Text Deskripsi Biasa -->
    <div class="rounded-xl bg-slate-50/70 border border-slate-200 p-4 sm:p-5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
            <i class="ri-file-list-2-line text-emerald-700 text-sm"></i>
            Deskripsi Pengaduan / Masalah:
        </h4>
        <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ trim($description) }}</div>
    </div>
@endif
