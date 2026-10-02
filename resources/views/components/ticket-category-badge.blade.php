@props([
    'category',
    'full' => false,
])

@php
    $categoryName = is_object($category) ? ($category->name ?? '') : (is_string($category) ? $category : '');
    $categorySlug = is_object($category) ? ($category->slug ?? '') : \Illuminate\Support\Str::slug($categoryName);

    // Map contextual Remix Icons
    $icon = match(true) {
        str_contains($categorySlug, 'jaringan') || str_contains($categorySlug, 'internet') => 'ri-wifi-line',
        str_contains($categorySlug, 'hardware') || str_contains($categorySlug, 'perangkat') => 'ri-computer-line',
        str_contains($categorySlug, 'aplikasi') || str_contains($categorySlug, 'software') => 'ri-apps-2-line',
        str_contains($categorySlug, 'printer') || str_contains($categorySlug, 'scanner') => 'ri-printer-line',
        str_contains($categorySlug, 'akun') || str_contains($categorySlug, 'email') || str_contains($categorySlug, 'keamanan') => 'ri-shield-keyhole-line',
        default => 'ri-folder-2-line',
    };

    // Clean, readable shortened labels for optimal table scanability while keeping full title in tooltip
    $shortLabel = match(true) {
        str_contains($categorySlug, 'jaringan') || str_contains($categorySlug, 'internet') => 'Jaringan & Internet',
        str_contains($categorySlug, 'hardware') || str_contains($categorySlug, 'perangkat') => 'Perangkat Keras',
        str_contains($categorySlug, 'aplikasi') || str_contains($categorySlug, 'software') => 'Aplikasi & Sistem',
        str_contains($categorySlug, 'printer') || str_contains($categorySlug, 'scanner') => 'Printer & Scanner',
        str_contains($categorySlug, 'akun') || str_contains($categorySlug, 'email') || str_contains($categorySlug, 'keamanan') => 'Akun & Keamanan',
        default => $categoryName,
    };

    $displayLabel = $full ? $categoryName : $shortLabel;
@endphp

<span 
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100/90 hover:bg-slate-200/70 text-slate-700 border border-slate-200/80 shadow-2xs transition-colors duration-150']) }}
    title="{{ $categoryName }}"
>
    <i class="{{ $icon }} text-xs text-slate-500 shrink-0"></i>
    <span class="truncate max-w-[170px] sm:max-w-none">{{ $displayLabel }}</span>
</span>
