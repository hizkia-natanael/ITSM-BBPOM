@props([
    'title',
    'message',
])

<div class="rounded-xl bg-rose-50/75 border border-rose-200/80 p-3.5 shadow-2xs flex items-start gap-3">
    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
        <i class="ri-error-warning-line text-base"></i>
    </div>
    <div class="min-w-0">
        <p class="text-xs font-bold text-rose-900 leading-snug">{{ $title }}</p>
        <p class="text-[11px] sm:text-xs text-rose-800/90 mt-0.5 leading-relaxed">{{ $message }}</p>
    </div>
</div>
