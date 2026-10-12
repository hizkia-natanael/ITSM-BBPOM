@props([
    'unhandledTickets' => collect(),
    'unhandledCount' => 0,
])

<style>
.bbpom-alert-scrollbar::-webkit-scrollbar { width: 4px; }
.bbpom-alert-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 9999px; }
.bbpom-alert-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
.bbpom-alert-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div style="flex: 1 1 0%; display: flex; flex-direction: column; justify-content: space-between; min-height: 0;">
    {{-- Card Body: Ticket List / Empty State --}}
    <div style="flex: 1 1 0%; max-height: 255px; overflow-y: auto; padding-right: 0.25rem;" class="bbpom-alert-scrollbar">
        @if ($unhandledCount > 0)
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach ($unhandledTickets as $ticket)
                    <div
                        style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.625rem; padding: 0.65rem 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03); transition: all 0.15s ease;"
                        onmouseover="this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';"
                        onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 1px 2px rgba(0,0,0,0.03)';"
                    >
                        {{-- Baris 1: No Tiket & Kategori --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.4rem; margin-bottom: 0.35rem;">
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <span style="font-family: monospace; font-size: 0.725rem; font-weight: 700; color: #065f46; background-color: #ecfdf5; padding: 0.1rem 0.4rem; border-radius: 0.35rem; border: 1px solid #a7f3d0;">
                                    {{ $ticket->ticket_number }}
                                </span>
                                <span style="font-size: 0.65rem; background-color: #f1f5f9; color: #475569; padding: 0.1rem 0.35rem; border-radius: 0.25rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $ticket->category?->name ?? 'Layanan TI' }}
                                </span>
                            </div>
                        </div>

                        {{-- Baris 2: Subjek Kendala --}}
                        <h4 style="margin: 0 0 0.3rem 0; font-size: 0.775rem; font-weight: 700; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $ticket->subject }}">
                            {{ $ticket->subject }}
                        </h4>

                        {{-- Baris 3: Pelapor --}}
                        <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.6875rem; color: #64748b; margin-bottom: 0.45rem;">
                            <i class="ri-user-3-line" style="color: #94a3b8; font-size: 0.75rem;"></i>
                            <span style="font-weight: 500; color: #475569; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $ticket->user?->name ?? 'Pegawai' }}</span>
                        </div>

                        {{-- Baris 4: Tombol Aksi --}}
                        <div style="display: flex; gap: 0.35rem; border-top: 1px solid #f8fafc; padding-top: 0.4rem;">
                            <button
                                type="button"
                                wire:click="claimTicket({{ $ticket->id }})"
                                style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; background-color: #059669; color: #ffffff; padding: 0.3rem 0.5rem; border: none; border-radius: 0.4rem; font-size: 0.7125rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;"
                                onmouseover="this.style.backgroundColor='#047857';"
                                onmouseout="this.style.backgroundColor='#059669';"
                                title="Ambil alih aduan ini langsung ke akun Anda"
                            >
                                <i class="ri-hand-coin-line" style="font-size: 0.8125rem;"></i>
                                <span>Tangani Sekarang</span>
                            </button>
                            <a
                                href="{{ url('/admin/tickets/' . $ticket->id . '/edit') }}"
                                style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1; border-radius: 0.4rem; text-decoration: none; transition: all 0.15s ease;"
                                onmouseover="this.style.backgroundColor='#e2e8f0'; this.style.color='#0f172a';"
                                onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#475569';"
                                title="Buka rincian tiket di tab editor"
                            >
                                <i class="ri-external-link-line" style="font-size: 0.8125rem;"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="height: 100%; min-height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 1.5rem 0.5rem;">
                <div style="width: 3rem; height: 3rem; border-radius: 9999px; background-color: #ecfdf5; border: 1px solid #a7f3d0; display: flex; align-items: center; justify-content: center; margin-bottom: 0.65rem; color: #059669;">
                    <i class="ri-shield-check-line" style="font-size: 1.65rem;"></i>
                </div>
                <h4 style="margin: 0 0 0.2rem 0; font-size: 0.8125rem; font-weight: 700; color: #064e3b;">
                    Antrean Aduan Terkendali
                </h4>
                <p style="margin: 0; font-size: 0.7125rem; color: #64748b; max-width: 14rem; line-height: 1.4;">
                    Tidak ada aduan yang menunggu penanganan Admin TIK saat ini.
                </p>
            </div>
        @endif
    </div>

    {{-- Card Footer: Desktop OS Web Push Controller --}}
    <x-desktop-push-banner />
</div>
