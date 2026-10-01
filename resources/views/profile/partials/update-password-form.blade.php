<section>
    <header class="border-b border-slate-100 pb-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            <h2 class="text-base font-bold text-slate-900">
                Perbarui Kata Sandi / Password
            </h2>
        </div>
        <p class="mt-1 text-xs text-slate-500 leading-relaxed">
            Pastikan akun Anda menggunakan kata sandi yang panjang dan aman demi menjaga kerahasiaan akses permohonan TIK.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 mb-1">
                Kata Sandi Saat Ini <span class="text-rose-500">*</span>
            </label>
            <input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="update_password_password" class="block text-xs font-semibold text-slate-700 mb-1">
                    Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input 
                    id="update_password_password" 
                    name="password" 
                    type="password" 
                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input 
                    id="update_password_password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <div class="pt-2 flex items-center gap-4">
            <button 
                type="submit" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 active:scale-[0.99] transition"
            >
                <i class="ri-lock-password-line text-sm"></i>
                Perbarui Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200"
                >
                    <i class="ri-checkbox-circle-fill text-sm"></i>
                    Kata sandi berhasil diperbarui
                </p>
            @endif
        </div>
    </form>
</section>
