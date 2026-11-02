@php
    $attributes = $attributes ?? new \Illuminate\View\ComponentAttributeBag();
@endphp

<div 
    {{ $attributes->merge(['class' => 'group select-none']) }}
    style="display: inline-flex !important; flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; gap: 0.65rem !important; height: 100% !important; max-height: 2.75rem !important; text-decoration: none !important; vertical-align: middle !important;"
>
    {{-- Logo BPOM Resmi --}}
    <img 
        src="{{ asset('images/logo-bpom.png') }}" 
        alt="Logo Resmi BPOM - ELITE Layanan Admin TIK BBPOM Di Semarang" 
        style="height: 2.15rem !important; max-height: 35px !important; width: auto !important; max-width: 44px !important; object-fit: contain !important; flex-shrink: 0 !important; display: block !important; transition: transform 0.2s ease;"
        class="group-hover:scale-105"
    >
    
    {{-- Tipografi Identitas Admin --}}
    <div style="display: flex !important; flex-direction: column !important; justify-content: center !important; text-align: left !important; line-height: 1.15 !important; flex-shrink: 0 !important; white-space: nowrap !important;">
        <div style="display: flex !important; align-items: center !important; gap: 0.35rem !important; line-height: 1 !important;">
            <span style="font-size: 0.95rem !important; font-weight: 900 !important; color: #047857 !important; letter-spacing: 0.04em !important; line-height: 1.1 !important;">ELITE</span>
            <span style="font-size: 0.625rem !important; font-weight: 700 !important; color: #64748b !important; letter-spacing: 0.02em !important; text-transform: uppercase !important; border-left: 1px solid #cbd5e1 !important; padding-left: 0.35rem !important; line-height: 1 !important;">{{ auth()->user()?->isKetuaTim() ? 'Ketua Tim' : 'Admin' }}</span>
        </div>
        <span style="display: block !important; font-size: 0.65rem !important; font-weight: 700 !important; color: #047857 !important; letter-spacing: 0.01em !important; line-height: 1.2 !important; margin-top: 1px !important; white-space: nowrap !important;">
            E-Layanan IT Terintegrasi
        </span>
    </div>
</div>
