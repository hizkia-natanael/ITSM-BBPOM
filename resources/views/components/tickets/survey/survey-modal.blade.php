@props(['ticket'])

<div
    x-data="ticketSurveyData()"
    @open-survey-modal.window="openModal()"
    @keydown.escape.window="closeModal()"
    class="relative z-50"
>
    <!-- Modal Backdrop & Sheet Container -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 overflow-y-auto z-50"
    >
        <div @click.outside="closeModal()" class="bg-white rounded-2xl border border-slate-200/90 shadow-2xl w-full max-w-xl overflow-hidden my-auto transform transition-all">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-xs flex items-center justify-center text-white border border-white/20 shadow-xs">
                        <i class="ri-survey-line text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black tracking-tight text-white">Survei Kepuasan Layanan</h3>
                        <p class="text-[11px] text-emerald-100">Tiket {{ $ticket->ticket_number }} &bull; Balai Besar POM di Semarang</p>
                    </div>
                </div>
                <button @click="closeModal()" type="button" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            <!-- Survey Form -->
            <form action="{{ route('tickets.rating.store', $ticket) }}" method="POST" @submit="isSubmitting = true" class="p-5 sm:p-6 space-y-4">
                @csrf
                <div class="space-y-3">
                    <x-tickets.survey.star-rating-input name="speed_rating" label="Kecepatan &amp; Ketanggapan Respons" description="Kecepatan penanganan sejak aduan dilaporkan hingga selesai" icon="ri-flashlight-line" />
                    <x-tickets.survey.star-rating-input name="quality_rating" label="Kualitas Solusi &amp; Ketuntasan Kendala" description="Efektivitas serta ketuntasan solusi teknis yang diberikan" icon="ri-shield-check-line" />
                    <x-tickets.survey.star-rating-input name="communication_rating" label="Komunikasi &amp; Keramahan Petugas" description="Kejelasan informasi, kesopanan, dan keramahan petugas TI" icon="ri-customer-service-2-line" />
                    <x-tickets.survey.star-rating-input name="system_rating" label="Kemudahan Sistem ELITE" description="Kemudahan antarmuka aplikasi dan alur pelaporan helpdesk" icon="ri-device-line" />
                </div>

                <div class="space-y-1.5 pt-1">
                    <label for="feedback" class="text-xs font-bold text-slate-700 flex items-center justify-between">
                        <span class="flex items-center gap-1.5"><i class="ri-chat-smile-2-line text-emerald-600"></i>Saran, Masukan, atau Apresiasi</span>
                        <span class="text-[10px] font-normal text-slate-400">Opsional</span>
                    </label>
                    <textarea id="feedback" name="feedback" rows="2" maxlength="1000" placeholder="Tuliskan pengalaman atau saran Anda untuk peningkatan layanan kami..." class="w-full text-xs rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 p-3 text-slate-800 placeholder-slate-400 transition"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <span class="text-[11px] font-medium text-slate-500">Rata-rata:</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 font-black text-xs">
                            <i class="ri-star-fill text-amber-500 text-xs"></i>
                            <span x-text="getAverage() + ' / 5.0'">5.0 / 5.0</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto">
                        <button type="button" @click="closeModal()" class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmitting" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 rounded-xl shadow-xs hover:shadow transition disabled:opacity-50">
                            <i class="ri-send-plane-fill"></i>
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Ulasan'">Kirim Ulasan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-tickets.survey.survey-modal-script />
</div>
