<section>
    <header class="border-b border-rose-100 pb-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
            <h2 class="text-base font-bold text-rose-900">
                Zona Bahaya: Hapus Akun
            </h2>
        </div>
        <p class="mt-1 text-xs text-slate-500 leading-relaxed">
            Setelah akun Anda dihapus, semua data dan histori tiket pengaduan akan dihapus secara permanen. Pastikan Anda telah menyimpan data yang diperlukan sebelum melanjutkan.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition"
    >
        <i class="ri-delete-bin-line text-sm"></i>
        Hapus Akun Pegawai
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-base font-bold text-slate-900">
                Apakah Anda yakin ingin menghapus akun ini?
            </h2>

            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi akun Anda untuk mengonfirmasi penghapusan permanen.
            </p>

            <div class="mt-5">
                <label for="password" class="sr-only">Kata Sandi</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 transition"
                    placeholder="Masukkan kata sandi Anda untuk konfirmasi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="mt-6 flex justify-end gap-2.5">
                <button 
                    type="button" 
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl transition"
                >
                    Batal
                </button>

                <button 
                    type="submit" 
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition"
                >
                    Ya, Hapus Akun Saya
                </button>
            </div>
        </form>
    </x-modal>
</section>
