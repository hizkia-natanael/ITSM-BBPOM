@php
    $unhandledCount = \App\Models\Ticket::whereNull('handled_by_id')->where('status', 'pending')->count();
@endphp

<div
    class="relative inline-flex items-center"
    x-data="adminNotificationBell()"
    x-init="init()"
>
    <a
        href="{{ url('/admin') }}"
        title="Lihat Aduan Belum Ditangani ({{ $unhandledCount }})"
        class="relative inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
    >
        <i class="ri-notification-3-line text-lg"></i>

        <template x-if="count > 0">
            <span class="absolute -top-1 -right-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white shadow-xs">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative" x-text="count"></span>
            </span>
        </template>
    </a>
</div>

<script>
function adminNotificationBell() {
    return {
        count: {{ $unhandledCount }},
        init() {
            // Periodic background check every 20 seconds
            setInterval(() => {
                fetch('{{ route('admin.tickets.unhandled.status') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.count = data.count;
                    })
                    .catch(() => {});
            }, 20000);
        }
    };
}
</script>
