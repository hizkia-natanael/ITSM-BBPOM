@php
    $serverTime = now();
    $serverTimestampMs = $serverTime->timestamp * 1000 + (int) ($serverTime->micro / 1000);
    $initialTimeString = $serverTime->format('H:i:s');
    $timezoneCode = 'WIB';
    $systemTimezone = config('app.timezone', 'Asia/Jakarta');
@endphp

<div
    x-data="bbpomSystemClock({{ $serverTimestampMs }}, '{{ $timezoneCode }}')"
    class="inline-flex items-center gap-1.5 sm:gap-2 h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl bg-slate-50/85 hover:bg-emerald-50/60 border border-slate-200/90 text-xs text-slate-700 shadow-2xs transition-all select-none"
    style="display: inline-flex; align-items: center; gap: 0.45rem; height: 2.35rem; padding: 0 0.7rem; border-radius: 0.75rem; background-color: rgba(248, 250, 252, 0.9); border: 1px solid rgba(226, 232, 240, 0.9); font-size: 0.75rem; color: #334155; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);"
    title="Waktu Sistem: {{ $systemTimezone }} (GMT+7) - Sinkron dengan Server TIK BBPOM"
>
    <i class="ri-time-line text-emerald-700 text-sm shrink-0" style="color: #047857; font-size: 0.875rem; line-height: 1;"></i>
    <span class="font-mono font-bold text-slate-800 tabular-nums text-xs" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-weight: 700; color: #1e293b; font-size: 0.75rem; font-variant-numeric: tabular-nums;" x-text="timeString">{{ $initialTimeString }}</span>
    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/70 uppercase leading-none shrink-0" style="display: inline-flex; align-items: center; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.625rem; font-weight: 700; background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; line-height: 1;">
        {{ $timezoneCode }}
    </span>
</div>

@once
    <script>
        window.bbpomSystemClock = function bbpomSystemClock(serverTimestampMs, timezoneLabel) {
            return {
                serverOffset: Number(serverTimestampMs) - Date.now(),
                timezone: timezoneLabel || 'WIB',
                timeString: '{{ $initialTimeString }}',
                timer: null,
                formatter: null,
                init() {
                    try {
                        this.formatter = new Intl.DateTimeFormat('id-ID', {
                            timeZone: 'Asia/Jakarta',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        });
                    } catch (err) {
                        this.formatter = null;
                    }
                    this.updateClock();
                    this.timer = setInterval(() => {
                        this.updateClock();
                    }, 1000);
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) {
                            this.updateClock();
                        }
                    });
                },
                updateClock() {
                    const currentMs = Date.now() + this.serverOffset;
                    const d = new Date(currentMs);
                    if (this.formatter) {
                        this.timeString = this.formatter.format(d).replace(/\./g, ':');
                    } else {
                        const pad = function(n) { return String(n).padStart(2, '0'); };
                        this.timeString = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
                    }
                }
            };
        };
    </script>
@endonce
