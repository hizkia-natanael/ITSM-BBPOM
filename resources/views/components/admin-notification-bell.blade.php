@php
    $initialUnhandled = \App\Models\Ticket::whereNull('handled_by_id')
        ->where('status', 'pending')
        ->with(['user', 'category'])
        ->latest()
        ->take(5)
        ->get()
        ->map(fn ($t) => [
            'id' => $t->id,
            'ticket_number' => $t->ticket_number,
            'user_name' => $t->user?->name ?? 'Pegawai BBPOM',
            'category' => $t->category?->name ?? 'Layanan TI',
            'subject' => $t->subject,
            'time_ago' => $t->created_at->diffForHumans(),
            'edit_url' => url('/admin/tickets/' . $t->id . '/edit'),
        ]);
    $initialCount = \App\Models\Ticket::whereNull('handled_by_id')->where('status', 'pending')->count();
@endphp

<div
    class="relative inline-flex items-center"
    x-data="bbpomNotificationBell({
        initialCount: {{ (int) $initialCount }},
        initialTickets: {{ Js::from($initialUnhandled) }},
        statusUrl: '{{ route('admin.tickets.unhandled.status') }}',
        dashboardUrl: '{{ url('/admin') }}',
        csrfToken: '{{ csrf_token() }}'
    })"
    x-init="init()"
>
    {{-- Trigger Bell Button --}}
    <button
        type="button"
        @click="toggleDropdown()"
        class="relative inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/80 transition-colors focus:outline-hidden"
        :aria-expanded="isOpen"
        title="Pusat Notifikasi Aduan TIK"
    >
        <i class="ri-notification-3-line text-lg"></i>

        <span
            x-show="hasCount()"
            x-cloak
            class="absolute -top-1 -right-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white shadow-xs"
        >
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
            <span class="relative" x-text="count"></span>
        </span>
    </button>

    {{-- Dropdown Popover --}}
    <div
        x-show="isOpen"
        x-cloak
        @click.outside="closeDropdown()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute right-0 top-full mt-2 w-84 sm:w-96 rounded-2xl bg-white/95 backdrop-blur-md border border-slate-200/80 shadow-2xl z-50 overflow-hidden"
        style="box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08);"
    >
        {{-- Popover Header --}}
        <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-100 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i class="ri-notification-badge-line text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800 leading-tight">Notifikasi Aduan</h4>
                    <p class="text-[11px] text-slate-500 leading-none mt-0.5">Antrean respon admin TIK</p>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                {{-- Sound Toggle Button --}}
                <button
                    type="button"
                    @click.stop="toggleSound()"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
                    :title="soundEnabled ? 'Suara notifikasi aktif (klik untuk mematikan)' : 'Suara notifikasi nonaktif (klik untuk membunyikan)'"
                >
                    <i :class="soundEnabled ? 'ri-volume-up-line' : 'ri-volume-mute-line'" class="text-sm"></i>
                </button>

                {{-- Count Badge --}}
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide"
                    :class="hasCount() ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                    x-text="count + ' aduan'"
                ></span>
            </div>
        </div>

        {{-- Toast Banner When In Claiming --}}
        <div
            x-show="claimToast"
            x-cloak
            class="px-3 py-2 bg-emerald-50 border-b border-emerald-100 text-[11px] text-emerald-800 font-medium flex items-center gap-1.5"
        >
            <i class="ri-checkbox-circle-fill text-emerald-600"></i>
            <span x-text="claimToast"></span>
        </div>

        {{-- Popover Ticket List Body --}}
        <div class="max-h-72 overflow-y-auto divide-y divide-slate-100">
            <template x-if="hasCount()">
                <div>
                    <template x-for="item in tickets" :key="item.id">
                        <div class="p-3 hover:bg-slate-50/80 transition-colors">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="font-mono text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded-md border border-emerald-200 shrink-0" x-text="item.ticket_number"></span>
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-md truncate" x-text="item.category"></span>
                                </div>
                                <span class="text-[10px] text-slate-400 shrink-0" x-text="item.time_ago"></span>
                            </div>

                            <p class="text-xs font-bold text-slate-800 line-clamp-1 mb-1" :title="item.subject" x-text="item.subject"></p>

                            <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-50">
                                <div class="flex items-center gap-1 text-[11px] text-slate-500 truncate">
                                    <i class="ri-user-3-line text-[10px] text-slate-400"></i>
                                    <span class="truncate" x-text="item.user_name"></span>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button
                                        type="button"
                                        @click="claimTicketDirect(item.id)"
                                        :disabled="isClaiming"
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition-colors cursor-pointer"
                                        title="Klaim dan mulai tangani aduan ini"
                                    >
                                        <i class="ri-hand-coin-line text-xs"></i>
                                        <span>Tangani</span>
                                    </button>

                                    <a
                                        :href="item.edit_url"
                                        class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors"
                                        title="Buka rincian aduan"
                                    >
                                        <i class="ri-external-link-line text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="!hasCount()">
                <div class="py-8 px-4 text-center">
                    <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mb-2">
                        <i class="ri-shield-check-line text-xl"></i>
                    </div>
                    <h5 class="text-xs font-bold text-emerald-950 mb-0.5">Antrean Terkendali</h5>
                    <p class="text-[11px] text-slate-500 max-w-xs mx-auto">Tidak ada aduan yang menunggu penanganan saat ini.</p>
                </div>
            </template>
        </div>

        {{-- Popover Footer --}}
        <div class="px-3 py-2 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between gap-2 text-[11px]">
            <span class="text-slate-400 flex items-center gap-1">
                <i class="ri-broadcast-line text-emerald-600"></i>
                <span>Polling live 15s</span>
            </span>

            <a
                :href="dashboardUrl"
                class="font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 transition-colors"
            >
                <span>Buka Dashboard</span>
                <i class="ri-arrow-right-line text-xs"></i>
            </a>
        </div>
    </div>
</div>

<script>
function bbpomNotificationBell(config) {
    return {
        isOpen: false,
        count: config.initialCount || 0,
        tickets: config.initialTickets || [],
        statusUrl: config.statusUrl,
        dashboardUrl: config.dashboardUrl,
        csrfToken: config.csrfToken,
        soundEnabled: localStorage.getItem('bbpom_notif_sound') !== 'disabled',
        isClaiming: false,
        claimToast: '',
        pollTimer: null,

        init() {
            this.startPolling();
            this.registerServiceWorkerGlobal();
        },

        hasCount() {
            return Number(this.count) > 0;
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchStatus(false);
            }
        },

        closeDropdown() {
            this.isOpen = false;
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('bbpom_notif_sound', this.soundEnabled ? 'enabled' : 'disabled');
            if (this.soundEnabled) {
                this.playChime();
            }
        },

        playChime() {
            if (!this.soundEnabled) return;
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const now = ctx.currentTime;

                // Note 1: E5 (659.25Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now);
                gain1.gain.setValueAtTime(0.15, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                // Note 2: B5 (987.77Hz)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(987.77, now + 0.1);
                gain2.gain.setValueAtTime(0.18, now + 0.1);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.1);
                osc2.stop(now + 0.55);
            } catch (e) {
                // AudioContext not allowed or not ready yet
            }
        },

        async fetchStatus(triggerSound) {
            try {
                const res = await fetch(this.statusUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();
                const previousCount = Number(this.count);
                const newCount = Number(data.count);

                this.count = newCount;
                this.tickets = data.tickets || [];

                if (triggerSound && newCount > previousCount) {
                    this.playChime();
                }
            } catch (e) {
                // Background polling fail silent
            }
        },

        startPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(() => {
                this.fetchStatus(true);
            }, 15000);
        },

        async claimTicketDirect(ticketId) {
            if (this.isClaiming) return;
            this.isClaiming = true;
            this.claimToast = '';

            try {
                const res = await fetch('/admin/tickets/' + ticketId + '/claim', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.claimToast = data.message;
                    await this.fetchStatus(false);
                    setTimeout(() => {
                        if (data.edit_url) {
                            window.location.href = data.edit_url;
                        }
                    }, 600);
                } else {
                    alert(data.message || 'Gagal mengambil alih tiket.');
                    await this.fetchStatus(false);
                }
            } catch (e) {
                alert('Terjadi kesalahan saat memproses klaim tiket.');
            } finally {
                this.isClaiming = false;
            }
        },

        async registerServiceWorkerGlobal() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
            try {
                const reg = await navigator.serviceWorker.register('/sw.js');
                await navigator.serviceWorker.ready;
                if (('Notification' in window) && Notification.permission === 'granted') {
                    const sub = await reg.pushManager.getSubscription();
                    if (sub) {
                        const subData = JSON.parse(JSON.stringify(sub));
                        await fetch('/admin/push/subscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                endpoint: sub.endpoint,
                                publicKey: subData.keys ? subData.keys.p256dh : null,
                                authToken: subData.keys ? subData.keys.auth : null,
                                contentEncoding: (window.PushManager && PushManager.supportedContentEncodings || ['aes128gcm'])[0]
                            })
                        });
                    }
                }
            } catch (err) {
                // Service worker registration silent fallback
            }
        }
    };
}
</script>
