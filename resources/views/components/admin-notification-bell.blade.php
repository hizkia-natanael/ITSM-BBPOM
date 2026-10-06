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
@include('filament.notifications.bell-styles')
</style>

<script>
@include('filament.notifications.bell-script')
</script>
