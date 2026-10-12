<div class="relative">
    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
        <span class="flex items-center gap-1.5">
            <i class="ri-user-line text-emerald-700"></i>
            <span>Nama Pegawai</span>
        </span>
        <span class="text-[11px] text-slate-400 font-normal">Pencarian Cepat</span>
    </label>

    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-10">
            <i class="ri-search-line"></i>
        </div>
        <input
            type="text"
            x-model="search"
            @focus="open = true"
            @input="open = true"
            @blur="closeSoon"
            @keydown.escape="open = false"
            @keydown.enter.prevent="selectFirst"
            @keydown.arrow-down.prevent="highlightNext"
            @keydown.arrow-up.prevent="highlightPrev"
            placeholder="Cari nama atau NIP..."
            autocomplete="off"
            :class="selectedEmail ? 'border-emerald-600 ring-2 ring-emerald-500/10 bg-emerald-50/20 font-medium' : 'border-slate-300 bg-slate-50/60 focus:bg-white focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10'"
            class="w-full pl-10 pr-10 py-3 border rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition"
        >

        <!-- Clear button -->
        <button
            type="button"
            x-show="selectedEmail"
            @mousedown.prevent="clearSelection"
            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-rose-600 transition"
            title="Hapus pilihan"
            style="display: none;"
        >
            <i class="ri-close-circle-fill text-base"></i>
        </button>

        <!-- Dropdown arrow -->
        <div x-show="!selectedEmail" class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
            <i class="ri-arrow-down-s-line text-base" x-show="!open"></i>
            <i class="ri-arrow-up-s-line text-base" x-show="open" style="display: none;"></i>
        </div>
    </div>

    <!-- Dropdown panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-120"
        x-transition:enter-start="opacity-0 -translate-y-1.5"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-80"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1.5"
        class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-900/15 overflow-hidden"
        style="display: none;"
    >
        <!-- Empty -->
        <div x-show="isResultsEmpty" class="px-4 py-5 text-center">
            <i class="ri-user-search-line text-slate-300 text-2xl mb-1 block"></i>
            <p class="text-xs text-slate-500 font-medium">Pegawai tidak ditemukan</p>
        </div>

        <!-- List -->
        <div x-show="hasResults" class="max-h-60 overflow-y-auto divide-y divide-slate-100">
            <template x-if="hasAdminUsers">
                <div>
                    <div class="px-3.5 py-1.5 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Administrator TI
                    </div>
                    <template x-for="user in adminUsers" :key="'admin-' + user.id">
                        <div
                            @mousedown.prevent="selectUser(user)"
                            @mouseenter="highlightedId = user.id"
                            :class="highlightedId === user.id ? 'bg-emerald-50 text-emerald-900' : 'hover:bg-slate-50'"
                            class="flex items-center justify-between gap-3 px-3.5 py-2.5 cursor-pointer transition-colors"
                        >
                            <span class="text-xs font-semibold text-slate-800 truncate" x-text="user.name"></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Admin</span>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="hasPegawaiUsers">
                <div>
                    <div class="px-3.5 py-1.5 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Pegawai
                    </div>
                    <template x-for="user in pegawaiUsers" :key="'pegawai-' + user.id">
                        <div
                            @mousedown.prevent="selectUser(user)"
                            @mouseenter="highlightedId = user.id"
                            :class="highlightedId === user.id ? 'bg-emerald-50 text-emerald-900' : 'hover:bg-slate-50'"
                            class="flex items-center justify-between gap-3 px-3.5 py-2.5 cursor-pointer transition-colors"
                        >
                            <span class="text-xs font-semibold text-slate-800 truncate" x-text="user.name"></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Pegawai</span>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
