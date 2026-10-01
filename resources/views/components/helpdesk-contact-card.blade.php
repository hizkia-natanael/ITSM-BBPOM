@props([
    'variant' => 'gradient', // 'gradient', 'dark', 'white'
    'title' => 'Kontak Darurat & Helpdesk TIK',
    'subtitle' => 'Balai Besar POM di Semarang',
    'showHours' => true,
    'showDescription' => true,
])

@php
    $isDark = $variant === 'dark';
    $isGradient = $variant === 'gradient';
    $isWhite = $variant === 'white';

    $cardClasses = match($variant) {
        'dark' => 'bg-slate-900 text-white border-slate-800 shadow-sm',
        'white' => 'bg-white text-slate-800 border-slate-200/80 shadow-xs',
        default => 'bg-gradient-to-br from-emerald-50 via-teal-50/50 to-white text-slate-800 border-emerald-200/80 shadow-xs',
    };

    $headerIconBg = $isDark ? 'bg-emerald-500/20 text-emerald-400' : 'bg-emerald-100 text-emerald-800';
    $titleClass = $isDark ? 'text-emerald-400' : 'text-emerald-900';
    $subTitleClass = $isDark ? 'text-slate-400' : 'text-slate-500';
    $bodyTextClass = $isDark ? 'text-slate-300' : 'text-slate-600';
    $highlightTextClass = $isDark ? 'text-emerald-400' : 'text-emerald-800 font-bold';
    $iconColor = $isDark ? 'text-emerald-400' : 'text-emerald-700';
    $dividerClass = $isDark ? 'border-slate-800 text-slate-400' : 'border-emerald-200/60 text-slate-500';
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl border p-6 space-y-4 print:hidden {$cardClasses}"]) }}>
    <!-- Header -->
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl {{ $headerIconBg }} flex items-center justify-center shrink-0 shadow-2xs">
            <i class="ri-customer-service-2-line text-lg"></i>
        </div>
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider {{ $titleClass }}">
                {{ $title }}
            </h4>
            <p class="text-[11px] {{ $subTitleClass }}">
                {{ $subtitle }}
            </p>
        </div>
    </div>

    <!-- Description Note -->
    @if($showDescription)
        <p class="text-xs {{ $bodyTextClass }} leading-relaxed">
            Bila sistem perkantoran lumpuh total, ada kendala darurat yang menghentikan pelayanan publik, atau kebutuhan acara mendesak, silakan langsung hubungi tim TIK di Ruang Pengelola TI.
        </p>
    @endif

    <!-- Contact List Details -->
    <div class="space-y-2.5 text-xs pt-1">
        <!-- Ekstensi Internal -->
        <div class="flex items-center gap-2.5 p-2 rounded-xl {{ $isDark ? 'bg-slate-800/60' : 'bg-white/80 border border-emerald-100' }}">
            <i class="ri-phone-fill {{ $iconColor }} text-sm shrink-0"></i>
            <div class="flex-1 flex items-center justify-between">
                <span class="{{ $bodyTextClass }} text-[11px]">Ekstensi Internal:</span>
                <span class="font-mono font-bold text-xs {{ $highlightTextClass }}">
                    104 / 105
                </span>
            </div>
        </div>

        <!-- Telepon Kantor / Hunting -->
        <div class="flex items-center gap-2.5 p-2 rounded-xl {{ $isDark ? 'bg-slate-800/60' : 'bg-white/80 border border-emerald-100' }}">
            <i class="ri-building-line {{ $iconColor }} text-sm shrink-0"></i>
            <div class="flex-1 flex items-center justify-between">
                <span class="{{ $bodyTextClass }} text-[11px]">Telepon Kantor:</span>
                <span class="font-medium text-[11px] {{ $isDark ? 'text-slate-200' : 'text-slate-800' }}">
                    (024) 7612305 &bull; Ext. 104/105
                </span>
            </div>
        </div>

        <!-- Lokasi Ruangan -->
        <div class="flex items-start gap-2.5 p-2 rounded-xl {{ $isDark ? 'bg-slate-800/60' : 'bg-white/80 border border-emerald-100' }}">
            <i class="ri-map-pin-2-fill {{ $iconColor }} text-sm shrink-0 mt-0.5"></i>
            <div>
                <span class="{{ $bodyTextClass }} text-[11px] block">Lokasi Unit TIK:</span>
                <span class="font-medium text-[11px] {{ $isDark ? 'text-slate-200' : 'text-slate-800' }}">
                    Ruang Pengelola TI / Subbag Tata Usaha Lt. 2, BBPOM Semarang
                </span>
            </div>
        </div>

        <!-- Jam Kerja Layanan -->
        @if($showHours)
            <div class="flex items-start gap-2.5 p-2 rounded-xl {{ $isDark ? 'bg-slate-800/60' : 'bg-white/80 border border-emerald-100' }}">
                <i class="ri-time-fill {{ $iconColor }} text-sm shrink-0 mt-0.5"></i>
                <div>
                    <span class="{{ $bodyTextClass }} text-[11px] block">Jam Operasional:</span>
                    <span class="font-medium text-[11px] {{ $isDark ? 'text-slate-200' : 'text-slate-800' }}">
                        Senin - Kamis: 07.30 - 16.00 WIB<br>
                        Jumat: 07.30 - 16.30 WIB
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Emergency Reminder Footer -->
    <div class="pt-2 border-t {{ $dividerClass }} text-[11px] leading-relaxed">
        <i class="ri-information-line {{ $iconColor }} me-0.5"></i>
        <span>Layanan bantuan teknologi informasi resmi untuk seluruh pegawai BBPOM di Semarang.</span>
    </div>
</div>
