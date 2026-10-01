@props(['items' => []])

<nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-2" aria-label="Breadcrumb">
    <ol class="inline-flex items-center gap-1.5 list-none p-0 m-0">
        @foreach($items as $index => $item)
            @php
                $isLast = $loop->last;
                $hasUrl = !empty($item['url']) && !$isLast;
                $icon = $item['icon'] ?? ($index === 0 ? 'ri-home-4-line' : null);
            @endphp
            <li class="inline-flex items-center gap-1.5">
                @if($index > 0)
                    <i class="ri-arrow-right-s-line text-slate-300 text-xs shrink-0" aria-hidden="true"></i>
                @endif

                @if($hasUrl)
                    <a href="{{ $item['url'] }}" class="inline-flex items-center gap-1 text-slate-500 hover:text-emerald-700 transition-colors">
                        @if($icon)
                            <i class="{{ $icon }} text-xs"></i>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-800 {{ !empty($item['mono']) ? 'font-mono' : '' }}" aria-current="page">
                        @if($icon)
                            <i class="{{ $icon }} text-xs"></i>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
