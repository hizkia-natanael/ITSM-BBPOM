<section>
    <header class="border-b border-slate-100 pb-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            <h2 class="text-base font-bold text-slate-900">
                Informasi Profil & Data Pegawai
            </h2>
        </div>
        <p class="mt-1 text-xs text-slate-500 leading-relaxed">
            Perbarui data identitas Anda. Data NIP, NIK, dan No. Telepon akan otomatis terisi pada setiap formulir pengajuan layanan TIK agar proses pengajuan lebih cepat.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                Nama Lengkap Pegawai <span class="text-rose-500">*</span>
            </label>
            <input 
                id="name" 
                name="name" 
                type="text" 
                class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                value="{{ old('name', $user->name) }}" 
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- NIP -->
            <div>
                <label for="nip" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nomor Induk Pegawai (NIP)
                </label>
                <input 
                    id="nip" 
                    name="nip" 
                    type="text" 
                    maxlength="25"
                    placeholder="Contoh: 198501012010121001"
                    class="w-full font-mono text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                    value="{{ old('nip', $user->nip) }}" 
                    autocomplete="off" 
                />
                <p class="text-[11px] text-slate-400 mt-1">18 digit NIP resmi BBPOM</p>
                <x-input-error class="mt-1.5" :messages="$errors->get('nip')" />
            </div>

            <!-- NIK -->
            <div>
                <label for="nik" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nomor Induk Kependudukan (NIK)
                </label>
                <input 
                    id="nik" 
                    name="nik" 
                    type="text" 
                    maxlength="25"
                    placeholder="Contoh: 3374012345678001"
                    class="w-full font-mono text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                    value="{{ old('nik', $user->nik) }}" 
                    autocomplete="off" 
                />
                <p class="text-[11px] text-slate-400 mt-1">16 digit KTP (untuk BSrE, dll)</p>
                <x-input-error class="mt-1.5" :messages="$errors->get('nik')" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Nomor Telepon / WA -->
            <div>
                <label for="phone_number" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nomor WhatsApp / Telepon Aktif
                </label>
                <input 
                    id="phone_number" 
                    name="phone_number" 
                    type="text" 
                    maxlength="25"
                    placeholder="Contoh: 081234567890"
                    class="w-full font-mono text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                    value="{{ old('phone_number', $user->phone_number) }}" 
                    autocomplete="tel" 
                />
                <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk konfirmasi tiket dan OTP</p>
                <x-input-error class="mt-1.5" :messages="$errors->get('phone_number')" />
            </div>

            <!-- Email Dinas / Akun -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 bg-white text-slate-900 px-3.5 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" 
                    value="{{ old('email', $user->email) }}" 
                    required 
                    autocomplete="username" 
                />
                <p class="text-[11px] text-slate-400 mt-1">Email aktif untuk notifikasi status tiket</p>
                <x-input-error class="mt-1.5" :messages="$errors->get('email')" />
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                <p class="text-xs text-amber-800">
                    Alamat email Anda belum diverifikasi.
                    <button form="send-verification" class="font-semibold underline hover:text-amber-900 ml-1">
                        Klik di sini untuk mengirim ulang link verifikasi email.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-xs text-emerald-700">
                        Link verifikasi baru telah dikirimkan ke alamat email Anda.
                    </p>
                @endif
            </div>
        @endif

        <div class="pt-2 flex items-center gap-4">
            <button 
                type="submit" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 active:scale-[0.99] transition"
            >
                <i class="ri-save-line text-sm"></i>
                Simpan Perubahan Data
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200"
                >
                    <i class="ri-checkbox-circle-fill text-sm"></i>
                    Data berhasil disimpan
                </p>
            @endif
        </div>
    </form>
</section>
