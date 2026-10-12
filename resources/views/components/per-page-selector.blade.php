@props([
    'id' => 'pagination_per_page',
    'value' => (int) request('per_page', 10),
    'hasSeparator' => true,
])

<div class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-slate-600">
    @if($hasSeparator)
        <span class="text-slate-300 select-none">|</span>
    @endif
    <label for="{{ $id }}" class="font-medium text-slate-700 whitespace-nowrap">Tampilkan:</label>
    <select
        id="{{ $id }}"
        name="per_page"
        class="text-xs sm:text-sm py-1.5 pl-3 pr-8 min-w-[4.75rem] rounded-xl border border-slate-300 bg-white text-slate-700 font-semibold focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-2xs cursor-pointer transition"
        onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.set('page', '1'); window.location.href = u.toString();"
    >
        @foreach([10, 25, 50, 100] as $count)
            <option value="{{ $count }}" {{ $value === $count ? 'selected' : '' }}>
                {{ $count }}
            </option>
        @endforeach
    </select>
    <span class="font-medium text-slate-700 whitespace-nowrap">per halaman</span>
</div>
