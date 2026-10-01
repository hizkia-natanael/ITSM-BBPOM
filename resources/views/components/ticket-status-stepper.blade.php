@props(['ticket'])

@php
    $status = $ticket->status;
    $isDitolak = $status === 'ditolak';

    $isStep1Done = true;
    $isStep2Done = in_array($status, ['diproses', 'selesai']);
    $isStep3Done = in_array($status, ['diproses', 'selesai']) && ($ticket->responses->where('user.role', 'admin')->count() > 0 || $status === 'selesai');
    $isStep4Done = $status === 'selesai';

    $progressPercent = match(true) {
        $isStep4Done => 100,
        $isStep3Done => 66.66,
        $isStep2Done => 33.33,
        default => 0,
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 print:hidden']) }}>
    <!-- Header Stepper -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">ITSM Lifecycle Tracking</span>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5 mt-0.5">
                <i class="ri-pulse-line text-emerald-700"></i>
                <span>Status Penanganan Kendala Layanan</span>
            </h3>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-500">Terakhir Diperbarui:</span>
            <span class="text-xs font-semibold text-slate-800 block">{{ $ticket->updated_at->translatedFormat('d M Y, H:i') }} WIB</span>
        </div>
    </div>

    @if($isDitolak)
        <!-- Status Ditolak / Dibatalkan Alert -->
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                <i class="ri-close-circle-fill text-2xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-rose-900 text-sm">Pengaduan Berstatus Ditolak / Tidak Dapat Diproses</h4>
                <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                    Permohonan atau pengaduan ini tidak dapat dilanjutkan oleh tim Helpdesk TIK BBPOM di Semarang. Alasan penolakan atau petunjuk perbaikan dapat Anda baca pada kolom riwayat tanggapan petugas di bawah.
                </p>
            </div>
        </div>
    @else
        <!-- 4-Stage Horizontal Stepper with Mathematically Bound Progress Bar -->
        <div class="relative py-2">
            <!-- Progress Track Container: starts exactly at Col 1 center (12.5%) and ends at Col 4 center (87.5%) -->
            <div class="hidden sm:block absolute top-6 left-[12.5%] right-[12.5%] -translate-y-1/2 h-1 z-0 overflow-hidden rounded-full">
                <!-- Gray Background Track -->
                <div class="w-full h-full bg-slate-200/80"></div>
                <!-- Active Emerald Bar -->
                <div 
                    class="absolute top-0 left-0 h-full bg-emerald-600 transition-all duration-700 ease-out"
                    style="width: {{ $progressPercent }}%;"
                ></div>
            </div>

            <!-- 4 Equidistant Columns (Grid cols 4) -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative z-10">
                
                <!-- Step 1: Pengajuan Masuk -->
                <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep1Done ? 'bg-emerald-700 text-white ring-4 ring-emerald-50' : 'bg-slate-100 text-slate-400' }}">
                        <i class="ri-file-text-line text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs sm:text-sm text-slate-900">1. Pengaduan Masuk</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Tiket tercatat di antrian Helpdesk</p>
                        <span class="text-[10px] font-mono text-slate-400 block mt-0.5">{{ $ticket->created_at->format('d/m/Y H:i') }} WIB</span>
                    </div>
                </div>

                <!-- Step 2: Verifikasi & Diagnosa -->
                <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep2Done ? ($status === 'diproses' && !$isStep3Done ? 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' : 'bg-emerald-700 text-white ring-4 ring-emerald-50') : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                        <i class="ri-search-eye-line text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs sm:text-sm {{ $isStep2Done ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                            2. Verifikasi & Diagnosa
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Penelaahan spesifikasi oleh tim IT</p>
                        @if($isStep2Done && $status === 'diproses' && !$isStep3Done)
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                Sedang Ditelaah
                            </span>
                        @endif
                        @if($ticket->handledBy && $isStep2Done)
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="ri-user-star-line text-emerald-600"></i>
                                <span>{{ $ticket->handledBy->name }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Step 3: Penanganan Teknis -->
                <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep3Done ? ($status === 'diproses' ? 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' : 'bg-emerald-700 text-white ring-4 ring-emerald-50') : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                        <i class="ri-tools-line text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs sm:text-sm {{ $isStep3Done ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                            3. Penanganan Teknis
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Eksekusi perbaikan oleh teknisi</p>
                        @if($isStep3Done && $status === 'diproses')
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                Proses Pengerjaan
                            </span>
                        @endif
                        @if($ticket->handledBy && $isStep3Done)
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="ri-tools-line text-blue-600"></i>
                                <span>{{ $ticket->handledBy->name }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Step 4: Selesai & Ditutup -->
                <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep4Done ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                        <i class="ri-checkbox-circle-line text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs sm:text-sm {{ $isStep4Done ? 'text-emerald-900 font-extrabold' : 'text-slate-400' }}">
                            4. Tuntas & Selesai
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kendala terselesaikan & tiket ditutup</p>
                        @if($isStep4Done)
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Tuntas
                            </span>
                            @if($ticket->handledBy)
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <i class="ri-checkbox-circle-line text-emerald-600"></i>
                                    <span>{{ $ticket->handledBy->name }}</span>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
