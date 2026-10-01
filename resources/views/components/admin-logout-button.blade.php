@php
    $attributes = $attributes ?? new \Illuminate\View\ComponentAttributeBag();
    $logoutUrl = filament()->getLogoutUrl();
@endphp

<form 
    action="{{ $logoutUrl }}" 
    method="POST" 
    {{ $attributes->merge(['class' => 'inline-flex items-center m-0 p-0']) }}
>
    @csrf
    <button
        type="submit"
        title="Keluar dari Panel Admin"
        style="
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #be123c;
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            box-shadow: 0 1px 2px rgba(225, 29, 72, 0.08);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            line-height: 1;
        "
        onmouseover="this.style.backgroundColor='#ffe4e6'; this.style.borderColor='#fda4af'; this.style.color='#9f1239'; this.style.transform='translateY(-1px)';"
        onmouseout="this.style.backgroundColor='#fff1f2'; this.style.borderColor='#fecdd3'; this.style.color='#be123c'; this.style.transform='translateY(0)';"
    >
        <i class="ri-logout-box-r-line" style="font-size: 0.875rem;"></i>
        <span>Keluar</span>
    </button>
</form>
