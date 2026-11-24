@props([
    'unhandledTickets' => collect(),
    'unhandledCount' => 0,
])

<style>
.bbpom-alert-scrollbar::-webkit-scrollbar { width: 4px; }
.bbpom-alert-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 9999px; }
.bbpom-alert-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
.bbpom-alert-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.bbpom-alert-root { flex: 1 1 0%; display: flex; flex-direction: column; min-height: 0; gap: 0.5rem; }
.bbpom-alert-list-stage { position: relative; min-height: 220px; }
.bbpom-alert-list-scroll { max-height: 360px; overflow-y: auto; padding-right: 0.25rem; padding-bottom: 0.75rem; }
.bbpom-alert-fade { display: none; }
@media (min-width: 1024px) {
    .bbpom-alert-list-stage { flex: 1 1 0%; min-height: 260px; }
    .bbpom-alert-list-scroll { position: absolute; inset: 0; max-height: none; padding-bottom: 1.5rem; }
    .bbpom-alert-fade { display: block; position: absolute; left: 0; right: 6px; bottom: 0; height: 1.5rem; pointer-events: none; background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.96)); }
}
.bbpom-alert-more-bar { display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding: 0.4rem 0.65rem; border-radius: 0.5rem; background-color: #ecfdf5; border: 1px solid #a7f3d0; font-size: 0.6875rem; color: #065f46; }
.bbpom-alert-more-link { display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 700; color: #047857; text-decoration: none; white-space: nowrap; }
.bbpom-alert-more-link:hover { color: #064e3b; text-decoration: underline; }
</style>

<div class="bbpom-alert-root">
    @if(auth()->user()?->isKetuaTim() && $unhandledCount > 0)
        <div style="padding: 0.65rem 0.75rem; border-radius: 0.75rem; background-color: #fffbeb; border: 1px solid #fde68a; display: flex; align-items: center; justify-content: space-between; gap: 0.65rem; box-shadow: 0 1px 2px rgba(217, 119, 6, 0.06);">
            <div style="display: flex; align-items: center; gap: 0.55rem; min-width: 0; flex: 1 1 auto;">
                <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background-color: #fef3c7; color: #92400e; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="ri-notification-3-line" style="font-size: 1rem;"></i>
                </div>
                <div style="min-width: 0; flex: 1 1 auto;">
                    <p style="margin: 0; font-size: 0.75rem; font-weight: 700; color: #78350f; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        Tindak Lanjut Tertunda
                    </p>
                    <p style="margin: 0.15rem 0 0; font-size: 0.675rem; color: #92400e; opacity: 0.9; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $unhandledCount }} tiket belum direspons teknisi
                    </p>
                </div>
            </div>
            <button type="button" wire:click="remindItTeam"
                style="flex-shrink: 0; display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 0.5rem; background-color: #d97706; color: #ffffff; font-size: 0.7rem; font-weight: 700; border: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.08);">
                <i class="ri-send-plane-fill" style="font-size: 0.75rem;"></i>
                <span>Ingatkan Tim TI</span>
            </button>
        </div>
    @endif

    <div class="bbpom-alert-list-stage">
        <div class="bbpom-alert-list-scroll bbpom-alert-scrollbar">
            @if ($unhandledCount > 0)
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach ($unhandledTickets as $ticket)
                        <x-unhandled-ticket-item :ticket="$ticket" />
                    @endforeach
                </div>
            @else
                <div style="height: 100%; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 1.5rem 0.5rem;">
                    <div style="width: 3rem; height: 3rem; border-radius: 9999px; background-color: #ecfdf5; border: 1px solid #a7f3d0; display: flex; align-items: center; justify-content: center; margin-bottom: 0.65rem; color: #059669;">
                        <i class="ri-shield-check-line" style="font-size: 1.65rem;"></i>
                    </div>
                    <h4 style="margin: 0 0 0.2rem; font-size: 0.8125rem; font-weight: 700; color: #064e3b;">
                        Antrean Aduan Terkendali
                    </h4>
                    <p style="margin: 0; font-size: 0.7125rem; color: #64748b; max-width: 14rem; line-height: 1.4;">
                        Tidak ada aduan yang menunggu penanganan Admin TIK saat ini.
                    </p>
                </div>
            @endif
        </div>
        @if ($unhandledCount > 0)
            <div class="bbpom-alert-fade" aria-hidden="true"></div>
        @endif
    </div>

    @if ($unhandledCount > $unhandledTickets->count())
        <div class="bbpom-alert-more-bar">
            <span>Menampilkan {{ $unhandledTickets->count() }} dari {{ $unhandledCount }} aduan</span>
            <a href="{{ \App\Filament\Resources\Tickets\TicketResource::getUrl('index', ['filters' => ['status' => ['value' => 'pending']]]) }}" class="bbpom-alert-more-link">
                <span>Lihat semua</span>
                <i class="ri-arrow-right-line"></i>
            </a>
        </div>
    @endif

    <x-desktop-push-banner />
</div>
