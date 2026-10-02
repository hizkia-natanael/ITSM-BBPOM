@props([
    'ticket' => null,
    'category' => null,
    'full' => false,
])

@php
    $servicesConfig = config('services_gateway.services', []);
    
    // Resolve ticket and service key
    $serviceKey = null;
    if ($ticket) {
        $serviceKey = $ticket->service_key_resolved ?? $ticket->service_key;
    }
    
    $service = ($serviceKey && isset($servicesConfig[$serviceKey])) ? $servicesConfig[$serviceKey] : null;

    $categoryObj = $category ?? ($ticket ? $ticket->category : null);
    $categoryName = is_object($categoryObj) ? ($categoryObj->name ?? '') : (is_string($categoryObj) ? $categoryObj : '');
    $categorySlug = is_object($categoryObj) ? ($categoryObj->slug ?? '') : \Illuminate\Support\Str::slug($categoryName);

    if (!$service && $categorySlug) {
        $cleanSlug = str_replace('-', '_', $categorySlug);
        if ($cleanSlug === 'sertifikat_bsre') {
            $cleanSlug = 'sertifikat_bse';
        }
        if (isset($servicesConfig[$cleanSlug])) {
            $service = $servicesConfig[$cleanSlug];
        }
    }

    if ($service) {
        $displayLabel = $service['name'];
        $icon = $service['icon'] ?? 'ri-apps-2-line';
        $titleTooltip = "Katalog Layanan: {$service['name']}";

        // Specific color theme based on service configuration
        $colorTheme = match($service['color'] ?? 'slate') {
            'indigo' => 'bg-indigo-50/90 text-indigo-700 border-indigo-200/80 hover:bg-indigo-100/70',
            'blue' => 'bg-blue-50/90 text-blue-700 border-blue-200/80 hover:bg-blue-100/70',
            'amber' => 'bg-amber-50/90 text-amber-800 border-amber-200/80 hover:bg-amber-100/70',
            'cyan' => 'bg-cyan-50/90 text-cyan-800 border-cyan-200/80 hover:bg-cyan-100/70',
            'sky' => 'bg-sky-50/90 text-sky-700 border-sky-200/80 hover:bg-sky-100/70',
            'emerald' => 'bg-emerald-50/90 text-emerald-800 border-emerald-200/80 hover:bg-emerald-100/70',
            'teal' => 'bg-teal-50/90 text-teal-800 border-teal-200/80 hover:bg-teal-100/70',
            'violet' => 'bg-violet-50/90 text-violet-700 border-violet-200/80 hover:bg-violet-100/70',
            'rose' => 'bg-rose-50/90 text-rose-700 border-rose-200/80 hover:bg-rose-100/70',
            'orange' => 'bg-orange-50/90 text-orange-800 border-orange-200/80 hover:bg-orange-100/70',
            default => 'bg-slate-100/90 text-slate-700 border-slate-200/80 hover:bg-slate-200/70',
        };
    } else {
        // Fallback for general/legacy tickets without catalog service key
        $icon = match(true) {
            str_contains($categorySlug, 'jaringan') || str_contains($categorySlug, 'internet') => 'ri-wifi-line',
            str_contains($categorySlug, 'hardware') || str_contains($categorySlug, 'perangkat') => 'ri-computer-line',
            str_contains($categorySlug, 'aplikasi') || str_contains($categorySlug, 'software') => 'ri-apps-2-line',
            str_contains($categorySlug, 'printer') || str_contains($categorySlug, 'scanner') => 'ri-printer-line',
            str_contains($categorySlug, 'akun') || str_contains($categorySlug, 'email') || str_contains($categorySlug, 'keamanan') => 'ri-shield-keyhole-line',
            default => 'ri-folder-2-line',
        };

        $shortLabel = match(true) {
            str_contains($categorySlug, 'jaringan') || str_contains($categorySlug, 'internet') => 'Jaringan & Internet',
            str_contains($categorySlug, 'hardware') || str_contains($categorySlug, 'perangkat') => 'Perangkat Keras',
            str_contains($categorySlug, 'aplikasi') || str_contains($categorySlug, 'software') => 'Aplikasi & Sistem',
            str_contains($categorySlug, 'printer') || str_contains($categorySlug, 'scanner') => 'Printer & Scanner',
            str_contains($categorySlug, 'akun') || str_contains($categorySlug, 'email') || str_contains($categorySlug, 'keamanan') => 'Akun & Keamanan',
            default => $categoryName,
        };

        $displayLabel = $full ? $categoryName : $shortLabel;
        $titleTooltip = $categoryName;
        $colorTheme = 'bg-slate-100/90 hover:bg-slate-200/70 text-slate-700 border-slate-200/80';
    }
@endphp

<span 
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border shadow-2xs transition-colors duration-150 ' . $colorTheme]) }}
    title="{{ $titleTooltip }}"
>
    <i class="{{ $icon }} text-xs shrink-0 opacity-80"></i>
    <span class="truncate max-w-[170px] sm:max-w-none">{{ $displayLabel }}</span>
</span>
