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

<div 
    x-data="bbpomTicketLifecycleTracker({
        ticketId: {{ $ticket->id }},
        initialStatus: '{{ $status }}',
        initialHandledBy: {{ $ticket->handledBy ? json_encode(['id' => $ticket->handledBy->id, 'name' => $ticket->handledBy->name]) : 'null' }},
        initialHasAdminResponse: {{ $isStep3Done ? 'true' : 'false' }},
        initialUpdatedAt: '{{ $ticket->updated_at->translatedFormat('d M Y, H:i') }} WIB',
        createdAtFormatted: '{{ $ticket->created_at->format('d/m/Y H:i') }} WIB'
    })"
    {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 print:hidden']) }}
>
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
            <span class="text-xs font-semibold text-slate-800 block" x-text="updatedAt">
                {{ $ticket->updated_at->translatedFormat('d M Y, H:i') }} WIB
            </span>
        </div>
    </div>

    <!-- Status Ditolak / Dibatalkan Alert -->
    <div x-show="isDitolak()" x-cloak class="rounded-xl bg-rose-50 border border-rose-200 p-5 flex items-start gap-4">
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

    <!-- 4-Stage Horizontal Stepper with Mathematically Bound Progress Bar -->
    <div x-show="!isDitolak()" class="relative py-2">
        <!-- Progress Track Container: starts exactly at Col 1 center (12.5%) and ends at Col 4 center (87.5%) -->
        <div class="hidden sm:block absolute top-6 left-[12.5%] right-[12.5%] -translate-y-1/2 h-1 z-0 overflow-hidden rounded-full">
            <!-- Gray Background Track -->
            <div class="w-full h-full bg-slate-200/80"></div>
            <!-- Active Emerald Bar -->
            <div 
                class="absolute top-0 left-0 h-full bg-emerald-600 transition-all duration-700 ease-out"
                :style="`width: ${progressPercent()}%`"
                style="width: {{ $progressPercent }}%;"
            ></div>
        </div>

        <!-- 4 Equidistant Columns (Grid cols 4) -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative z-10">
            
            <!-- Step 1: Pengajuan Masuk -->
            <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all bg-emerald-700 text-white ring-4 ring-emerald-50">
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
                <div 
                    class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all"
                    :class="step2IconClass()"
                >
                    <i class="ri-search-eye-line text-xl"></i>
                </div>
                <div>
                    <h4 
                        class="text-xs sm:text-sm"
                        :class="isStep2Done() ? 'text-slate-900 font-bold' : 'text-slate-400 font-bold'"
                    >
                        2. Verifikasi & Diagnosa
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Penelaahan spesifikasi oleh tim IT</p>
                    <template x-if="isStep2Active()">
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            Sedang Ditelaah
                        </span>
                    </template>
                    <template x-if="handledBy && isStep2Done()">
                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            <i class="ri-user-star-line text-emerald-600"></i>
                            <span x-text="handledBy.name"></span>
                        </span>
                    </template>
                </div>
            </div>

            <!-- Step 3: Penanganan Teknis -->
            <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                <div 
                    class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all"
                    :class="step3IconClass()"
                >
                    <i class="ri-tools-line text-xl"></i>
                </div>
                <div>
                    <h4 
                        class="text-xs sm:text-sm"
                        :class="isStep3Done() ? 'text-slate-900 font-bold' : 'text-slate-400 font-bold'"
                    >
                        3. Penanganan Teknis
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Eksekusi perbaikan oleh teknisi</p>
                    <template x-if="isStep3Active()">
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            Proses Pengerjaan
                        </span>
                    </template>
                    <template x-if="handledBy && isStep3Done()">
                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            <i class="ri-tools-line text-blue-600"></i>
                            <span x-text="handledBy.name"></span>
                        </span>
                    </template>
                </div>
            </div>

            <!-- Step 4: Selesai & Ditutup -->
            <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                <div 
                    class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all"
                    :class="step4IconClass()"
                >
                    <i class="ri-checkbox-circle-line text-xl"></i>
                </div>
                <div>
                    <h4 
                        class="text-xs sm:text-sm"
                        :class="isStep4Done() ? 'text-emerald-900 font-extrabold' : 'text-slate-400 font-bold'"
                    >
                        4. Tuntas & Selesai
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kendala terselesaikan & tiket ditutup</p>
                    <template x-if="isStep4Done()">
                        <div class="flex flex-col items-center gap-1">
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Tuntas
                            </span>
                            <template x-if="handledBy">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <i class="ri-checkbox-circle-line text-emerald-600"></i>
                                    <span x-text="handledBy.name"></span>
                                </span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
window.bbpomTicketLifecycleTracker = function bbpomTicketLifecycleTracker(config) {
    const cfg = config || {};
    return {
        ticketId: cfg.ticketId || null,
        status: cfg.initialStatus || 'pending',
        handledBy: cfg.initialHandledBy || null,
        hasAdminResponse: Boolean(cfg.initialHasAdminResponse),
        updatedAt: cfg.initialUpdatedAt || '',

        init() {
            window.addEventListener('ticket-status-updated', (event) => {
                const detail = event.detail;
                if (!detail || Number(detail.ticketId) !== Number(this.ticketId)) {
                    return;
                }
                this.status = detail.status;
                if (detail.handledBy !== undefined) {
                    this.handledBy = detail.handledBy;
                }
                if (detail.hasAdminResponse !== undefined) {
                    this.hasAdminResponse = Boolean(detail.hasAdminResponse);
                }
                if (detail.updatedAtFormatted) {
                    this.updatedAt = detail.updatedAtFormatted;
                }
            });
        },

        isDitolak() {
            return this.status === 'ditolak';
        },

        isStep1Done() {
            return true;
        },

        isStep2Done() {
            return this.status === 'diproses' || this.status === 'selesai';
        },

        isStep3Done() {
            if (this.status === 'selesai') return true;
            return this.status === 'diproses' && this.hasAdminResponse;
        },

        isStep4Done() {
            return this.status === 'selesai';
        },

        isStep2Active() {
            return this.status === 'diproses' && !this.isStep3Done();
        },

        isStep3Active() {
            return this.status === 'diproses' && this.isStep3Done();
        },

        progressPercent() {
            if (this.isStep4Done()) return 100;
            if (this.isStep3Done()) return 66.66;
            if (this.isStep2Done()) return 33.33;
            return 0;
        },

        step2IconClass() {
            if (!this.isStep2Done()) {
                return 'bg-slate-100 text-slate-400 border border-slate-200';
            }
            if (this.status === 'diproses' && !this.isStep3Done()) {
                return 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse';
            }
            return 'bg-emerald-700 text-white ring-4 ring-emerald-50';
        },

        step3IconClass() {
            if (!this.isStep3Done()) {
                return 'bg-slate-100 text-slate-400 border border-slate-200';
            }
            if (this.status === 'diproses') {
                return 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse';
            }
            return 'bg-emerald-700 text-white ring-4 ring-emerald-50';
        },

        step4IconClass() {
            if (this.isStep4Done()) {
                return 'bg-emerald-600 text-white ring-4 ring-emerald-100';
            }
            return 'bg-slate-100 text-slate-400 border border-slate-200';
        }
    };
};
</script>
