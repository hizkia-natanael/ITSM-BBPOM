@php
    $attributes = $attributes ?? new \Illuminate\View\ComponentAttributeBag();
    $currentUser = auth()->user();
    $roleLabel = match (true) {
        $currentUser?->isKetuaTim() => 'Ketua Tim',
        $currentUser?->isAdmin() => 'Admin',
        default => 'User',
    };
@endphp

<div 
    {{ $attributes->merge(['class' => 'group select-none bbpom-brand-wrapper']) }}
>
    {{-- Logo Resmi BPOM (Selalu Tampil: Terpusat saat Sidebar Tertutup, Berdampingan saat Terbuka) --}}
    <img 
        src="{{ asset('images/logo-bpom.png') }}" 
        alt="Logo Resmi BPOM - ELITE Layanan Admin TIK BBPOM Di Semarang" 
        class="bbpom-brand-img group-hover:scale-105"
    >

    {{-- Blok Identitas ELITE & Micro-Pill Peran (Hanya Muncul Saat Sidebar Terbuka) --}}
    <div class="bbpom-brand-text">
        <div class="bbpom-brand-title-row">
            <span class="bbpom-brand-name">ELITE</span>
            <span class="bbpom-brand-divider" aria-hidden="true"></span>
            <span class="bbpom-brand-role-badge">{{ $roleLabel }}</span>
        </div>
        <span class="bbpom-brand-subtitle">
            E-Layanan IT Terintegrasi
        </span>
    </div>
</div>
