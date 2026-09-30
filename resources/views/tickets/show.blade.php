<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
                        </li>
                        <li>
                            <span class="mx-1 text-gray-400">/</span>
                        </li>
                        <li>
                            <a href="{{ route('tickets.index') }}" class="hover:text-emerald-700">Daftar Pengaduan</a>
                        </li>
                        <li>
                            <span class="mx-1 text-gray-400">/</span>
                        </li>
                        <li class="font-semibold text-emerald-800">{{ $ticket->ticket_number }}</li>
                    </ol>
                </nav>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-xl text-gray-900 leading-tight">
                        Tiket {{ $ticket->ticket_number }}
                    </h2>
                    <x-ticket-status-badge :status="$ticket->status" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Visual Timeline Status Tracking -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-6">
                Status Pelacakan Kendala (Tracking Timeline)
            </h3>

            @if($ticket->status === 'ditolak')
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-rose-900 text-sm">Pengaduan Berstatus Ditolak / Dibatalkan</h4>
                        <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                            Pengaduan ini tidak dapat diproses lebih lanjut oleh tim IT BBPOM. Silakan periksa kolom tanggapan petugas di bawah untuk melihat alasan penolakan atau ajukan tiket baru dengan data yang sesuai.
                        </p>
                    </div>
                </div>
            @else
                <div class="relative">
                    <!-- Progress Bar Background Line -->
                    <div class="hidden sm:block absolute top-1/2 left-10 right-10 -translate-y-1/2 h-1 bg-gray-200 z-0"></div>
                    
                    @php
                        $isPendingActive = in_array($ticket->status, ['pending', 'diproses', 'selesai']);
                        $isDiprosesActive = in_array($ticket->status, ['diproses', 'selesai']);
                        $isSelesaiActive = $ticket->status === 'selesai';
                    @endphp

                    <!-- Active Progress Line -->
                    <div class="hidden sm:block absolute top-1/2 left-10 -translate-y-1/2 h-1 bg-emerald-600 z-0 transition-all duration-500"
                         style="width: {{ $isSelesaiActive ? 'calc(100% - 80px)' : ($isDiprosesActive ? 'calc(50% - 40px)' : '0%') }};">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 relative z-10">
                        <!-- Step 1: Pending -->
                        <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition {{ $isPendingActive ? 'bg-amber-500 text-white ring-4 ring-amber-100' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm text-gray-900">1. Pengaduan Masuk</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Tiket diterbitkan & masuk antrian IT</p>
                                <span class="text-[10px] text-gray-400 block">{{ $ticket->created_at->format('d/m/Y H:i') }} WIB</span>
                            </div>
                        </div>

                        <!-- Step 2: Diproses -->
                        <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition {{ $isDiprosesActive ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm {{ $isDiprosesActive ? 'text-blue-900 font-extrabold' : 'text-gray-500' }}">2. Sedang Diproses</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Penanganan & investigasi teknisi</p>
                                @if($isDiprosesActive)
                                    <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">Aktif</span>
                                @endif
                            </div>
                        </div>

                        <!-- Step 3: Selesai -->
                        <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition {{ $isSelesaiActive ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm {{ $isSelesaiActive ? 'text-emerald-900 font-extrabold' : 'text-gray-500' }}">3. Selesai Ditangani</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Kendala tuntas diperbaiki</p>
                                @if($isSelesaiActive)
                                    <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">Tuntas</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri (2 Kolom): Detail Tiket & Riwayat Diskusi -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card Detail Tiket (Read-only) -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7">
                    <div class="border-b border-gray-100 pb-4 mb-5 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Detail Informasi</span>
                            <h3 class="text-base font-bold text-gray-900 mt-0.5">{{ $ticket->subject }}</h3>
                        </div>
                        <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                            {{ $ticket->category->name }}
                        </span>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="font-semibold text-gray-500 block mb-1">Deskripsi Masalah / Kendala:</span>
                            <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-100 text-gray-800 text-xs sm:text-sm leading-relaxed whitespace-pre-line">
                                {{ $ticket->description }}
                            </div>
                        </div>

                        <!-- Lampiran jika ada -->
                        @if($ticket->attachment)
                            <div class="pt-3">
                                <span class="font-semibold text-gray-500 block mb-1">Berkas Lampiran:</span>
                                <div class="inline-flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-gray-50/50">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-xs">Dokumen / Bukti Lampiran</p>
                                        <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="text-emerald-700 hover:text-emerald-800 font-semibold underline text-[11px] inline-flex items-center gap-1 mt-0.5">
                                            Buka / Unduh Lampiran
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kolom Diskusi & Tanggapan Admin/User -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7">
                    <div class="border-b border-gray-100 pb-4 mb-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Kolom Diskusi & Riwayat Tanggapan
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Pantau jawaban teknisi IT dan kirimkan informasi tambahan terkait pengaduan ini
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                            {{ $ticket->responses->count() }} Balasan
                        </span>
                    </div>

                    <!-- List Responses -->
                    <div class="space-y-4 mb-6">
                        @forelse($ticket->responses as $response)
                            @php
                                $isAdmin = $response->user->isAdmin();
                            @endphp
                            <div class="rounded-xl p-4 {{ $isAdmin ? 'bg-emerald-50/80 border border-emerald-200' : 'bg-gray-50 border border-gray-200' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs {{ $isAdmin ? 'bg-emerald-700 text-white' : 'bg-gray-700 text-white' }}">
                                            {{ strtoupper(substr($response->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-gray-900">{{ $response->user->name }}</span>
                                            @if($isAdmin)
                                                <span class="ms-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-200 text-emerald-800">
                                                    Petugas IT BBPOM
                                                </span>
                                            @else
                                                <span class="ms-1.5 px-2 py-0.5 rounded text-[10px] font-medium bg-gray-200 text-gray-700">
                                                    Pelapor
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-gray-500">
                                        {{ $response->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                </div>
                                <div class="text-xs text-gray-800 leading-relaxed ps-9 whitespace-pre-line">
                                    {{ $response->response_text }}
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center bg-gray-50/60 rounded-xl border border-dashed border-gray-200">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <p class="text-xs font-semibold text-gray-700">Belum Ada Tanggapan</p>
                                <p class="text-[11px] text-gray-500 max-w-sm mx-auto mt-0.5">
                                    Petugas IT BBPOM sedang menelaah pengaduan Anda. Anda juga dapat menambahkan catatan atau respon melalui form di bawah.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Form Tambah Balasan / Tanggapan -->
                    <form method="POST" action="{{ route('tickets.reply', $ticket) }}" class="pt-4 border-t border-gray-100">
                        @csrf
                        <div class="space-y-3">
                            <label for="response_text" class="block text-xs font-semibold text-gray-700">
                                Berikan Balasan / Informasi Tambahan
                            </label>
                            <textarea 
                                id="response_text" 
                                name="response_text" 
                                rows="3" 
                                class="w-full rounded-xl border-gray-300 text-xs sm:text-sm text-gray-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-gray-400"
                                placeholder="Ketik tanggapan Anda di sini untuk berkomunikasi dengan staf IT..."
                                required
                            >{{ old('response_text') }}</textarea>
                            <x-input-error :messages="$errors->get('response_text')" />
                            
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kirim Tanggapan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan (1 Kolom): Metadata Card -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Ringkasan Tiket</h4>
                    
                    <div class="divide-y divide-gray-100 text-xs">
                        <div class="py-2.5 flex justify-between">
                            <span class="text-gray-500">Nomor Tiket:</span>
                            <span class="font-bold text-gray-900">{{ $ticket->ticket_number }}</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-gray-500">Status Saat Ini:</span>
                            <x-ticket-status-badge :status="$ticket->status" />
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-gray-500">Kategori:</span>
                            <span class="font-semibold text-gray-800">{{ $ticket->category->name }}</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-gray-500">Pelapor:</span>
                            <span class="font-medium text-gray-800">{{ $ticket->user->name }}</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-gray-500">Email Pelapor:</span>
                            <span class="text-gray-700">{{ $ticket->user->email }}</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-gray-500">Waktu Dibuat:</span>
                            <span class="text-gray-700">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-gray-500">Terakhir Update:</span>
                            <span class="text-gray-700">{{ $ticket->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-100/70 rounded-2xl p-5 border border-slate-200/60 text-xs text-slate-600 space-y-2">
                    <h5 class="font-bold text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Layanan
                    </h5>
                    <p class="leading-relaxed">
                        Jika penanganan telah selesai dan kendala telah teratasi, staf IT BBPOM akan menandai tiket ini sebagai <strong>Selesai</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
