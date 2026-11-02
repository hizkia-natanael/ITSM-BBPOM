@props([
    'id' => 'pagination_per_page',
    'value' => (int) request('per_page', 10),
    'hasSeparator' => true,
])

<div class="flex items-center gap-1.5 text-xs sm:text-sm text-slate-600">
    @if($hasSeparator)
        <span class="text-slate-300 select-none">|</span>
    @endif
    <label for="{{ $id }}" class="font-medium text-slate-700 whitespace-nowrap cursor-pointer">Tampilkan:</label>
    <div class="relative inline-flex items-center">
        <select
            id="{{ $id }}"
            name="per_page"
            style="background-image: none !important;"
            class="appearance-none bg-white text-xs sm:text-sm py-1 pl-2.5 pr-6 w-[3.75rem] rounded-lg border border-slate-300 text-slate-700 font-semibold hover:border-slate-400 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-2xs cursor-pointer transition text-left"
            onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.set('page', '1'); window.location.href = u.toString();"
        >
            @foreach([10, 25, 50, 100] as $count)
                <option value="{{ $count }}" {{ $value === $count ? 'selected' : '' }}>
                    {{ $count }}
                </option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1.5 text-slate-400">
            <i class="ri-arrow-down-s-line text-sm"></i>
        </div>
    </div>
    <span class="font-medium text-slate-700 whitespace-nowrap">per halaman</span>
</div>
