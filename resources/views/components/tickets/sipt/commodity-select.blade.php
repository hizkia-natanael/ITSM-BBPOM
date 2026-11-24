@props([
    'id' => 'sipt_commodity',
    'name' => 'sipt_commodity',
    'label' => 'Komoditi',
    'required' => true,
    'helperText' => null,
])

@php
    $commodities = [
        'Pangan',
        'Obat',
        'Obat Tradisional',
        'Kosmetik',
        'Suplemen Kesehatan',
        'Lainnya',
    ];
    $selectedValue = old($name);
@endphp

<div>
    <x-input-label :for="$id" class="font-semibold text-xs text-slate-700">
        <span>{{ $label }}</span>
        @if($required)
            <span class="text-rose-600">*</span>
        @endif
    </x-input-label>
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if($required) required @endif
        class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs transition"
    >
        <option value="">-- Pilih Komoditi --</option>
        @foreach($commodities as $commodity)
            <option value="{{ $commodity }}" @selected($selectedValue === $commodity)>{{ $commodity }}</option>
        @endforeach
    </select>
    @if($helperText)
        <p class="text-[11px] text-slate-500 mt-1">{{ $helperText }}</p>
    @endif
</div>
