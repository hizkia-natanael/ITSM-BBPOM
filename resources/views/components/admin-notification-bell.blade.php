<div
    class="bbpom-admin-bell-wrapper relative inline-flex items-center"
    x-data="bbpomNotificationBell({
        statusUrl: '{{ route('admin.tickets.unhandled.status') }}',
        readItemUrl: '{{ route('admin.notifications.read-item') }}',
        readAllUrl: '{{ route('admin.notifications.read-all') }}',
        ticketsUrl: '{{ url('/admin/tickets') }}',
        csrfToken: '{{ csrf_token() }}'
    })"
    x-init="init()"
>
    {{-- Trigger Bell Button --}}
    <button
        type="button"
        @click="toggleDropdown()"
        class="bbpom-admin-bell-btn relative inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/80 transition-all focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20"
        style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; position: relative; display: inline-flex; align-items: center; justify-content: center;"
        :aria-expanded="isOpen"
        title="Pusat Notifikasi Admin TIK"
        aria-label="Pusat Notifikasi Admin TIK"
    >
        <i class="ri-notification-3-line text-lg" style="font-size: 1.125rem; line-height: 1;"></i>

        <span
            x-show="hasCount()"
            x-cloak
            class="bbpom-admin-bell-count absolute flex items-center justify-center rounded-full bg-rose-600 text-white shadow-xs"
            style="position: absolute; top: 2px; right: 2px; display: inline-flex; align-items: center; justify-content: center; min-width: 1.125rem; height: 1.125rem; padding: 0 0.25rem; border-radius: 9999px; background-color: #e11d48; color: #ffffff; font-size: 0.625rem; font-weight: 900; line-height: 1; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"
        >
            <span class="bbpom-admin-bell-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75" style="position: absolute; inset: 0; border-radius: 9999px; background-color: #fb7185; opacity: 0.75; animation: bbpomPing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
            <span class="relative" style="position: relative; z-index: 1;" x-text="count"></span>
        </span>
    </button>

    {{-- Dropdown Popover matching user portal reference --}}
    <div
        x-show="isOpen"
        x-cloak
        @click.outside="closeDropdown()"
        @keydown.escape.window="closeDropdown()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="bbpom-admin-popover absolute right-0 top-full mt-2 w-84 max-w-[calc(100vw-1.5rem)] sm:w-96 rounded-3xl bg-white/95 backdrop-blur-md border border-slate-200/80 shadow-2xl z-50 overflow-hidden"
    >
        {{-- Header: Notifikasi on left, sound toggle + tandai dibaca + lihat semua on right --}}
        <div class="bbpom-admin-popover-header px-5 py-4 bg-white border-b border-slate-100 flex items-center justify-between gap-2" style="white-space: nowrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; min-width: 0;">
                <h4 class="bbpom-admin-header-title text-sm font-bold text-slate-900 tracking-tight" style="white-space: nowrap; margin: 0;">Notifikasi</h4>
                <template x-if="hasCount()">
                    <span class="bbpom-admin-badge-new inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200" style="white-space: nowrap; flex-shrink: 0;" x-text="count + ' baru'"></span>
                </template>
            </div>

            <div class="bbpom-admin-header-actions flex items-center gap-2.5 text-xs" style="white-space: nowrap; flex-shrink: 0;">
                {{-- Sound Toggle Button --}}
                <button
                    type="button"
                    @click.stop="toggleSound()"
                    class="bbpom-admin-sound-btn inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
                    :title="soundEnabled ? 'Suara notifikasi aktif (klik untuk membisukan)' : 'Suara notifikasi nonaktif (klik untuk membunyikan)'"
                    style="flex-shrink: 0;"
                >
                    <i :class="soundEnabled ? 'ri-volume-up-line' : 'ri-volume-mute-line'" style="font-size: 0.875rem;"></i>
                </button>

                <button
                    type="button"
                    x-show="hasCount()"
                    x-cloak
                    @click="markAllRead()"
                    class="bbpom-admin-mark-all-btn text-[11px] font-medium text-slate-500 hover:text-emerald-700 transition-colors"
                    style="white-space: nowrap; flex-shrink: 0;"
                >
                    Tandai dibaca
                </button>

                <a
                    :href="ticketsUrl"
                    class="bbpom-admin-view-all-link text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors"
                    style="white-space: nowrap; flex-shrink: 0;"
                >
                    Lihat Semua
                </a>
            </div>
        </div>

        {{-- Toast Banner When In Claiming --}}
        <div
            x-show="claimToast"
            x-cloak
            class="bbpom-admin-toast px-4 py-2 bg-emerald-50 border-b border-emerald-100 text-[11px] text-emerald-800 font-medium flex items-center gap-1.5"
        >
            <i class="ri-checkbox-circle-fill" style="color: #059669; font-size: 0.875rem;"></i>
            <span x-text="claimToast"></span>
        </div>

        {{-- Popover Items Feed --}}
        <div class="bbpom-admin-feed max-h-80 overflow-y-auto divide-y divide-slate-100/80">
            <template x-if="hasItems()">
                <div>
                    <template x-for="item in items" :key="item.id">
                        <div
                            class="bbpom-admin-item p-4 transition-colors group relative flex items-start gap-3.5"
                            :class="item.is_read ? 'read bg-white hover:bg-slate-50/80' : 'unread bg-emerald-50/35 hover:bg-emerald-50/60'"
                            @click="onCardClick(item, $event)"
                            style="cursor: pointer;"
                        >
                            {{-- Circular colored icon badge matching screenshot --}}
                            <div
                                class="bbpom-admin-avatar w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-2xs transition-transform group-hover:scale-105"
                                :class="item.item_type === 'ticket' ? 'bbpom-admin-avatar-amber' : 'bbpom-admin-avatar-sky'"
                            >
                                <i :class="item.icon"></i>
                            </div>

                            {{-- Center text content --}}
                            <div class="bbpom-admin-content flex-1 min-w-0 pr-1">
                                <div class="bbpom-admin-title-row flex items-center justify-between gap-2">
                                    <h5
                                        class="bbpom-admin-item-title text-xs font-bold text-slate-900 group-hover:text-emerald-700 truncate leading-snug transition-colors"
                                        x-text="item.title"
                                    ></h5>
                                </div>
                                <p class="bbpom-admin-item-desc text-[11px] text-slate-600 line-clamp-2 mt-0.5 leading-relaxed" x-text="item.description"></p>

                                {{-- Quick action bar for unhandled tickets --}}
                                <template x-if="item.can_claim">
                                    <div class="bbpom-admin-actions-row flex items-center gap-2 mt-2 pt-2 border-t border-slate-100">
                                        <button
                                            type="button"
                                            @click.stop="claimTicketDirect(item.raw_id)"
                                            :disabled="isClaiming"
                                            class="bbpom-admin-claim-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition-colors cursor-pointer disabled:opacity-50"
                                            title="Klaim dan mulai tangani aduan ini"
                                        >
                                            <i class="ri-hand-coin-line" style="font-size: 0.75rem;"></i>
                                            <span>Tangani</span>
                                        </button>

                                        <span
                                            class="bbpom-admin-detail-btn inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-medium text-slate-600 group-hover:text-emerald-700 transition-colors"
                                            title="Buka rincian tiket"
                                        >
                                            <span>Rincian</span>
                                            <i class="ri-arrow-right-s-line" style="font-size: 0.75rem;"></i>
                                        </span>
                                    </div>
                                </template>

                                <span class="bbpom-admin-time block text-[10px] font-medium text-slate-400 mt-1" x-text="item.time_ago"></span>
                            </div>

                            {{-- Unread Dot Indicator on Right Side matching screenshot --}}
                            <template x-if="!item.is_read">
                                <span
                                    class="bbpom-admin-unread-dot w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0 self-center ring-4 ring-emerald-500/15"
                                    title="Belum dibaca"
                                ></span>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="!hasItems()">
                <div class="bbpom-admin-empty py-10 px-4 text-center">
                    <div class="bbpom-admin-empty-icon w-12 h-12 mx-auto rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mb-2.5">
                        <i class="ri-notification-badge-line text-2xl"></i>
                    </div>
                    <h5 class="bbpom-admin-empty-title text-xs font-bold text-slate-800 mb-0.5">Belum Ada Notifikasi</h5>
                    <p class="bbpom-admin-empty-desc text-[11px] text-slate-500 max-w-xs mx-auto">Aduan masuk yang belum ditangani dan pesan baru dari pelapor akan muncul di sini.</p>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
.bbpom-admin-bell-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.bbpom-admin-bell-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    color: #475569;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.bbpom-admin-bell-btn:hover {
    color: #047857;
    background-color: #ecfdf5;
}
.bbpom-admin-bell-count {
    position: absolute;
    top: 2px;
    right: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.125rem;
    height: 1.125rem;
    padding: 0 0.25rem;
    border-radius: 9999px;
    background-color: #e11d48;
    color: #ffffff;
    font-size: 0.625rem;
    font-weight: 900;
    line-height: 1;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.bbpom-admin-bell-ping {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background-color: #fb7185;
    opacity: 0.75;
    animation: bbpomPing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}
@keyframes bbpomPing {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}
.bbpom-admin-popover {
    position: absolute;
    right: 0;
    top: calc(100% + 0.5rem);
    width: 25.5rem;
    max-width: calc(100vw - 1.5rem);
    border-radius: 1.25rem;
    background-color: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 20px 30px -8px rgba(15, 23, 42, 0.18), 0 8px 12px -4px rgba(15, 23, 42, 0.08);
    z-index: 99999;
    overflow: hidden;
    font-family: inherit;
    text-align: left;
}
.bbpom-admin-popover-header {
    padding: 0.875rem 1.125rem;
    background-color: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    white-space: nowrap;
}
.bbpom-admin-header-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.01em;
    margin: 0;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-badge-new {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 700;
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    white-space: nowrap;
    flex-shrink: 0;
    line-height: 1;
}
.bbpom-admin-header-actions {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-sound-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 0.5rem;
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.bbpom-admin-sound-btn:hover {
    color: #047857;
    background-color: #ecfdf5;
}
.bbpom-admin-mark-all-btn {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 0.6875rem;
    font-weight: 500;
    cursor: pointer;
    transition: color 0.15s ease;
    padding: 0;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-mark-all-btn:hover {
    color: #047857;
}
.bbpom-admin-view-all-link {
    font-size: 0.75rem;
    font-weight: 600;
    color: #059669;
    text-decoration: none;
    transition: color 0.15s ease;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-view-all-link:hover {
    color: #047857;
}
.bbpom-admin-toast {
    padding: 0.5rem 1rem;
    background-color: #ecfdf5;
    border-bottom: 1px solid #d1fae5;
    color: #065f46;
    font-size: 0.6875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}
.bbpom-admin-feed {
    max-height: 22rem;
    overflow-y: auto;
}
.bbpom-admin-feed::-webkit-scrollbar {
    width: 4px;
}
.bbpom-admin-feed::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.bbpom-admin-item {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.15s ease;
    position: relative;
    text-decoration: none;
}
.bbpom-admin-item.unread {
    background-color: rgba(236, 253, 245, 0.45);
}
.bbpom-admin-item.read {
    background-color: #ffffff;
}
.bbpom-admin-item:hover {
    background-color: #f8fafc;
}
.bbpom-admin-item.unread:hover {
    background-color: rgba(236, 253, 245, 0.7);
}
.bbpom-admin-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.125rem;
    transition: transform 0.15s ease;
}
.bbpom-admin-item:hover .bbpom-admin-avatar {
    transform: scale(1.05);
}
.bbpom-admin-avatar-amber {
    background-color: #fef3c7;
    color: #b45309;
    border: 1px solid rgba(253, 230, 138, 0.8);
}
.bbpom-admin-avatar-sky {
    background-color: #e0f2fe;
    color: #0369a1;
    border: 1px solid rgba(186, 230, 253, 0.8);
}
.bbpom-admin-content {
    flex: 1;
    min-width: 0;
    padding-right: 0.25rem;
}
.bbpom-admin-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.bbpom-admin-item-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
    text-decoration: none;
    margin: 0;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.bbpom-admin-item-title:hover {
    color: #047857;
}
.bbpom-admin-item-desc {
    font-size: 0.6875rem;
    color: #475569;
    line-height: 1.45;
    margin: 0.25rem 0 0 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.bbpom-admin-actions-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f1f5f9;
}
.bbpom-admin-claim-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    font-size: 0.6875rem;
    font-weight: 700;
    background-color: #059669;
    color: #ffffff;
    border: none;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    transition: background-color 0.15s ease;
}
.bbpom-admin-claim-btn:hover {
    background-color: #047857;
}
.bbpom-admin-claim-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.bbpom-admin-detail-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.6875rem;
    font-weight: 500;
    color: #475569;
    text-decoration: none;
    transition: all 0.15s ease;
}
.bbpom-admin-detail-btn:hover {
    color: #047857;
    background-color: #f1f5f9;
}
.bbpom-admin-time {
    display: block;
    font-size: 0.625rem;
    font-weight: 500;
    color: #94a3b8;
    margin-top: 0.35rem;
}
.bbpom-admin-unread-dot {
    width: 0.625rem;
    height: 0.625rem;
    border-radius: 9999px;
    background-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
    flex-shrink: 0;
    align-self: center;
    margin-left: 0.25rem;
}
.bbpom-admin-empty {
    padding: 2.5rem 1rem;
    text-align: center;
}
.bbpom-admin-empty-icon {
    width: 3rem;
    height: 3rem;
    margin: 0 auto 0.625rem auto;
    border-radius: 9999px;
    background-color: #ecfdf5;
    border: 1px solid #d1fae5;
    color: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.bbpom-admin-empty-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}
.bbpom-admin-empty-desc {
    font-size: 0.6875rem;
    color: #64748b;
    margin: 0 auto;
    max-width: 16rem;
    line-height: 1.4;
}
</style>

<script>
function bbpomNotificationBell(config) {
    return {
        isOpen: false,
        count: 0,
        items: [],
        statusUrl: config.statusUrl,
        readItemUrl: config.readItemUrl,
        readAllUrl: config.readAllUrl,
        ticketsUrl: config.ticketsUrl,
        csrfToken: config.csrfToken,
        soundEnabled: localStorage.getItem('bbpom_notif_sound') !== 'disabled',
        isClaiming: false,
        claimToast: '',
        pollTimer: null,

        init() {
            this.fetchStatus(false);
            this.startPolling();
            this.registerServiceWorkerGlobal();
        },

        hasCount() {
            return Number(this.count) > 0;
        },

        hasItems() {
            return Array.isArray(this.items) && this.items.length > 0;
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
                // AudioContext silent fallback
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
                this.items = data.items || [];

                if (triggerSound && newCount > previousCount) {
                    this.playChime();
                }
            } catch (e) {
                // Polling fail silent
            }
        },

        startPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(() => {
                this.fetchStatus(true);
            }, 15000);
        },

        async onCardClick(item, event) {
            const url = item.url || item.edit_url;
            if (!url) return;

            await this.clickItem(item, event);

            if (event && (event.ctrlKey || event.metaKey || event.button === 1)) {
                window.open(url, '_blank');
                return;
            }

            window.location.href = url;
        },

        async clickItem(item, event) {
            if (!item.is_read) {
                item.is_read = true;
                if (Number(this.count) > 0) {
                    this.count = Number(this.count) - 1;
                }
                try {
                    fetch(this.readItemUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({
                            item_type: item.item_type,
                            id: item.raw_id
                        }),
                        keepalive: true
                    });
                } catch (e) {
                    // Abaikan kesalahan koneksi
                }
            }
        },

        async markAllRead() {
            try {
                await fetch(this.readAllUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });
            } catch (e) {
                // Abaikan kesalahan koneksi
            }
            await this.fetchStatus(false);
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
