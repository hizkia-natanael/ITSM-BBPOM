@php
    $attributes = $attributes ?? new \Illuminate\View\ComponentAttributeBag();
@endphp

<div 
    {{ $attributes->merge(['class' => 'group select-none']) }}
    style="display: inline-flex !important; flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; gap: 0.75rem !important; height: 100% !important; max-height: 2.75rem !important; text-decoration: none !important; vertical-align: middle !important;"
>
    {{-- Logo BPOM Resmi --}}
    <img 
        src="{{ asset('images/logo-bpom.png') }}" 
        alt="Logo Resmi BPOM - Layanan Admin TIK BBPOM di Semarang" 
        style="height: 2.25rem !important; max-height: 36px !important; width: auto !important; max-width: 48px !important; object-fit: contain !important; flex-shrink: 0 !important; display: block !important; transition: transform 0.2s ease;"
        class="group-hover:scale-105"
    >
    
    {{-- Tipografi Identitas Admin --}}
    <div style="display: flex !important; flex-direction: column !important; justify-content: center !important; text-align: left !important; line-height: 1.15 !important; flex-shrink: 0 !important; white-space: nowrap !important;">
        <span style="display: block !important; font-size: 0.875rem !important; font-weight: 800 !important; color: #0f172a !important; letter-spacing: -0.015em !important; line-height: 1.2 !important; white-space: nowrap !important;">
            Layanan Admin TIK
        </span>
        <span style="display: block !important; font-size: 0.6875rem !important; font-weight: 700 !important; color: #047857 !important; text-transform: uppercase !important; letter-spacing: 0.04em !important; line-height: 1.2 !important; margin-top: 2px !important; white-space: nowrap !important;">
            BBPOM Di Semarang
        </span>
    </div>
</div>
