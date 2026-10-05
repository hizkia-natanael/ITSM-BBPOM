<x-user-push-helper />

<div
    class="relative inline-flex items-center"
    x-data="bbpomUserReplyBell({
        statusUrl: '{{ route('notifications.replies') }}',
        readUrl: '{{ route('notifications.replies.read') }}',
        readItemUrl: '{{ route('notifications.read-item') }}',
        ticketsUrl: '{{ route('tickets.index') }}',
        csrfToken: '{{ csrf_token() }}'
    })"
    x-init="init()"
>
    {{-- Trigger Bell Button --}}
    <button
        type="button"
        @click="toggleDropdown()"
        class="bbpom-bell-trigger-btn relative inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/80 transition-all focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20"
        style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; position: relative; display: inline-flex; align-items: center; justify-content: center;"
        :aria-expanded="isOpen"
        title="Notifikasi Balasan Tim IT"
        aria-label="Notifikasi Balasan Tim IT"
    >
        <i class="ri-notification-3-line text-lg" style="font-size: 1.125rem; line-height: 1;"></i>

        <span
            x-show="hasCount()"
            x-cloak
            class="bbpom-bell-badge-count absolute flex items-center justify-center rounded-full bg-rose-600 text-white shadow-xs"
            style="position: absolute; top: 2px; right: 2px; display: inline-flex; align-items: center; justify-content: center; min-width: 1.125rem; height: 1.125rem; padding: 0 0.25rem; border-radius: 9999px; background-color: #e11d48; color: #ffffff; font-size: 0.625rem; font-weight: 900; line-height: 1; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"
        >
            <span class="bbpom-bell-badge-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75" style="position: absolute; inset: 0; border-radius: 9999px; background-color: #fb7185; opacity: 0.75; animation: bbpomPing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
            <span class="relative" style="position: relative; z-index: 1;" x-text="count"></span>
        </span>
    </button>

    {{-- Dropdown Popover --}}
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
        class="bbpom-user-popover absolute right-0 top-full mt-2 w-[25.5rem] max-w-[calc(100vw-1.5rem)] rounded-3xl bg-white border border-slate-200/90 shadow-2xl z-50 overflow-hidden"
        style="width: 25.5rem; max-width: calc(100vw - 1.5rem); background-color: #ffffff; box-shadow: 0 20px 30px -8px rgba(15, 23, 42, 0.18), 0 8px 12px -4px rgba(15, 23, 42, 0.08); z-index: 9999;"
    >
        {{-- Header matching reference: "Notifikasi" on left, "Lihat Semua" / "Tandai Dibaca" on right --}}
        <div class="px-5 py-4 bg-white border-b border-slate-100 flex items-center justify-between gap-2.5 whitespace-nowrap" style="background-color: #ffffff;">
            <div class="flex items-center gap-2 shrink-0 min-w-0">
                <h4 class="text-sm font-bold text-slate-900 tracking-tight whitespace-nowrap" style="margin: 0; color: #0f172a;">Notifikasi</h4>
                <template x-if="hasCount()">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 whitespace-nowrap shrink-0" style="background-color: #d1fae5; color: #065f46;" x-text="count + ' baru'"></span>
                </template>
            </div>

            <div class="flex items-center gap-3 text-xs whitespace-nowrap shrink-0">
                <button
                    type="button"
                    x-show="hasCount()"
                    x-cloak
                    @click="markAllRead()"
                    class="text-[11px] font-medium text-slate-500 hover:text-emerald-700 transition-colors whitespace-nowrap shrink-0"
                    style="cursor: pointer;"
                >
                    Tandai dibaca
                </button>
                <a
                    :href="ticketsUrl"
                    class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors whitespace-nowrap shrink-0"
                    style="color: #059669;"
                >
                    Lihat Semua
                </a>
            </div>
        </div>

        {{-- Desktop Web Push Opt-in Strip --}}
        <div class="px-5 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-3" style="background-color: #f8fafc;" x-show="pushState !== 'unsupported'" x-cloak>
            <div class="flex items-center gap-2 min-w-0">
                <i class="text-sm shrink-0" :class="pushIconClass()"></i>
                <span class="text-[11px] text-slate-600 leading-snug truncate" x-text="pushLabel()"></span>
            </div>
            <button
                type="button"
                x-show="pushCanToggle()"
                x-cloak
                @click="togglePush()"
                :disabled="pushLoading"
                class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-colors disabled:opacity-60"
                :class="pushState === 'on' ? 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' : 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 shadow-2xs'"
                x-text="pushLoading ? '...' : (pushState === 'on' ? 'Matikan' : 'Aktifkan Pop-up')"
            ></button>
        </div>

        {{-- Notification Feed matching reference layout --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100/80 bg-white" style="background-color: #ffffff;">
            <template x-if="hasItems()">
                <div style="background-color: #ffffff;">
                    <template x-for="item in items" :key="item.id">
                        <a
                            :href="item.url"
                            @click="clickItem(item, $event)"
                            class="flex items-start gap-3.5 p-4 transition-colors group relative border-b border-slate-100/80 last:border-b-0"
                            :class="item.is_read ? 'hover:bg-slate-50' : 'hover:bg-emerald-50/70'"
                            :style="item.is_read ? 'background-color: #ffffff;' : 'background-color: #f0fdf4;'"
                        >
                            {{-- Circular colored icon badge matching screenshot --}}
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-2xs transition-transform group-hover:scale-105"
                                :style="item.item_type === 'ticket' || item.type === 'event' ? (item.badge_color === 'amber' ? 'background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;' : (item.badge_color === 'rose' ? 'background-color: #ffe4e6; color: #be123c; border: 1px solid #fecdd3;' : 'background-color: #d1fae5; color: #047857; border: 1px solid #a7f3d0;')) : 'background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;'"
                            >
                                <i :class="item.icon" class="text-lg"></i>
                            </div>

                            {{-- Center text content --}}
                            <div class="flex-1 min-w-0 pr-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h5 class="text-xs font-bold text-slate-900 truncate leading-snug group-hover:text-emerald-700 transition-colors" x-text="item.title" style="color: #0f172a;"></h5>
                                </div>
                                <p class="text-[11px] text-slate-600 line-clamp-2 mt-0.5 leading-relaxed" x-text="item.description" style="color: #475569;"></p>
                                <span class="block text-[10px] font-medium text-slate-400 mt-1" x-text="item.time_ago" style="color: #94a3b8;"></span>
                            </div>

                            {{-- Unread Dot Indicator on Right Side matching screenshot --}}
                            <template x-if="!item.is_read">
                                <span
                                    class="w-2.5 h-2.5 rounded-full shrink-0 self-center ring-4 ring-emerald-500/20"
                                    style="background-color: #10b981; width: 0.625rem; height: 0.625rem; border-radius: 9999px;"
                                    title="Belum dibaca"
                                ></span>
                            </template>
                        </a>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="!hasItems()">
                <div class="py-10 px-4 text-center bg-white" style="background-color: #ffffff;">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mb-2.5" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #059669;">
                        <i class="ri-notification-badge-line text-2xl"></i>
                    </div>
                    <h5 class="text-xs font-bold text-slate-800 mb-0.5" style="color: #1e293b;">Belum Ada Notifikasi</h5>
                    <p class="text-[11px] text-slate-500 max-w-xs mx-auto" style="color: #64748b;">Pembaruan status tiket (diproses, selesai, ditolak) dan balasan tim IT akan muncul di sini.</p>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function bbpomUserReplyBell(config) {
    return {
        isOpen: false,
        count: 0,
        items: [],
        statusUrl: config.statusUrl,
        readUrl: config.readUrl,
        readItemUrl: config.readItemUrl,
        ticketsUrl: config.ticketsUrl,
        csrfToken: config.csrfToken,
        pollTimer: null,
        pushState: 'unsupported',
        pushLoading: false,

        init() {
            this.fetchStatus();
            this.pollTimer = setInterval(() => this.fetchStatus(), 20000);
            this.refreshPushState();
            window.bbpomUserPush.onChange((state) => { this.pushState = state; });
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
                this.fetchStatus();
                this.refreshPushState();
            }
        },

        closeDropdown() {
            this.isOpen = false;
        },

        async fetchStatus() {
            try {
                const res = await fetch(this.statusUrl, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                this.count = Number(data.count);
                this.items = data.items || [];
            } catch (e) {
                // Polling gagal: abaikan.
            }
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
                await fetch(this.readUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken }
                });
            } catch (e) {
                // Abaikan.
            }
            await this.fetchStatus();
        },

        async refreshPushState() {
            this.pushState = await window.bbpomUserPush.getState();
        },

        pushCanToggle() {
            return this.pushState === 'on' || this.pushState === 'off';
        },

        pushIconClass() {
            if (this.pushState === 'on') return 'ri-notification-badge-line text-emerald-600';
            if (this.pushState === 'denied') return 'ri-forbid-line text-rose-500';
            return 'ri-notification-off-line text-slate-400';
        },

        pushLabel() {
            if (this.pushState === 'on') return 'Pop-up desktop aktif di browser ini.';
            if (this.pushState === 'denied') return 'Notifikasi diblokir browser. Izinkan lewat ikon gembok di bilah alamat.';
            return 'Aktifkan pop-up desktop saat tim IT membalas.';
        },

        async togglePush() {
            if (this.pushLoading) return;
            this.pushLoading = true;
            try {
                this.pushState = this.pushState === 'on'
                    ? await window.bbpomUserPush.disable()
                    : await window.bbpomUserPush.enable();
            } catch (e) {
                await this.refreshPushState();
            } finally {
                this.pushLoading = false;
            }
        }
    };
}
</script>

<style>
@keyframes bbpomPing {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}
</style>
