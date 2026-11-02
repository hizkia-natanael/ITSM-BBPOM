@props([
    'name',
    'label',
    'description' => null,
    'icon' => 'ri-star-line',
])

<div class="p-3.5 sm:p-4 rounded-xl bg-slate-50/80 border border-slate-200/80 hover:border-emerald-300 transition-colors">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="space-y-0.5">
            <label class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-1.5">
                <i class="{{ $icon }} text-emerald-600"></i>
                <span>{{ $label }}</span>
            </label>
            @if($description)
                <p class="text-[11px] text-slate-500 leading-tight">
                    {{ $description }}
                </p>
            @endif
        </div>

        <!-- Hidden input for form submission -->
        <input type="hidden" name="{{ $name }}" :value="ratings['{{ $name }}']" required>

        <!-- Star Buttons 1 to 5 -->
        <div class="flex items-center gap-1 shrink-0 self-start sm:self-auto bg-white px-2.5 py-1.5 rounded-lg border border-slate-200 shadow-2xs">
            @for ($i = 1; $i <= 5; $i++)
                <button
                    type="button"
                    @click="setStar('{{ $name }}', {{ $i }})"
                    @mouseenter="hoverStar('{{ $name }}', {{ $i }})"
                    @mouseleave="leaveStar('{{ $name }}')"
                    class="p-1 rounded transition-transform hover:scale-110 focus:outline-hidden"
                    :title="'Bintang ' + {{ $i }}"
                >
                    <i
                        class="text-lg transition-colors"
                        :class="isStarActive('{{ $name }}', {{ $i }}) ? 'ri-star-fill text-amber-400 drop-shadow-2xs' : 'ri-star-line text-slate-300 hover:text-amber-300'"
                    ></i>
                </button>
            @endfor

            <!-- Live Text Description of Current Rating -->
            <span
                class="ml-2 text-[11px] font-bold min-w-[70px] text-right"
                :class="getRatingColor('{{ $name }}')"
                x-text="getRatingLabel('{{ $name }}')"
            ></span>
        </div>
    </div>
</div>
