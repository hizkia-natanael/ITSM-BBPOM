<!-- 2. NIP -->
<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
        <span class="flex items-center gap-1.5">
            <i class="ri-id-card-line text-slate-500"></i>
            <span>NIP Pegawai</span>
        </span>
        <span class="text-[11px] text-slate-400 font-normal">Auto-filled</span>
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <i class="ri-barcode-line"></i>
        </div>
        <input
            type="text"
            :value="selectedNip"
            placeholder="Terisi otomatis..."
            readonly
            class="w-full pl-10 pr-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono text-slate-700 shadow-sm cursor-default"
        >
    </div>
</div>

<!-- 3. Password -->
<div x-data="{ showPassword: false }">
    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
        <span class="flex items-center gap-1.5">
            <i class="ri-lock-password-line text-slate-500"></i>
            <span>Kata Sandi</span>
        </span>
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <i class="ri-key-line"></i>
        </div>
        <input
            id="password"
            :type="showPassword ? 'text' : 'password'"
            name="password"
            required
            autocomplete="current-password"
            placeholder="Masukkan password..."
            class="w-full pl-10 pr-11 py-3 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition"
        >
        <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-800 transition"
        >
            <i x-show="!showPassword" class="ri-eye-line text-base"></i>
            <i x-show="showPassword" class="ri-eye-off-line text-base text-emerald-700" style="display: none;"></i>
        </button>
    </div>
</div>
