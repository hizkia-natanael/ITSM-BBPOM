@props([
    'ticket',
])

<div 
    style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between; gap: 0.55rem; transition: all 0.15s ease; box-sizing: border-box;"
    onmouseover="this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';"
    onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 1px 2px rgba(0,0,0,0.03)';"
>
    <div>
        {{-- Baris 1: No Tiket, Kategori & Waktu Pengajuan --}}
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: nowrap;">
            <div style="display: flex; align-items: center; gap: 0.35rem; min-width: 0; flex: 1 1 auto; overflow: hidden;">
                <span style="font-family: monospace; font-size: 0.7rem; font-weight: 700; color: #065f46; background-color: #ecfdf5; padding: 0.15rem 0.45rem; border-radius: 0.35rem; border: 1px solid #a7f3d0; flex-shrink: 0; white-space: nowrap;">
                    {{ $ticket->ticket_number }}
                </span>
                <span style="font-size: 0.65rem; font-weight: 600; background-color: #f1f5f9; color: #475569; padding: 0.15rem 0.4rem; border-radius: 0.25rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;" title="{{ $ticket->category?->name ?? 'Layanan TI' }}">
                    {{ $ticket->category?->name ?? 'Layanan TI' }}
                </span>
            </div>
            <span style="font-size: 0.65rem; color: #64748b; font-weight: 500; flex-shrink: 0; white-space: nowrap;">
                {{ $ticket->created_at?->diffForHumans(null, true) }}
            </span>
        </div>

        {{-- Baris 2: Subjek Kendala (Mendukung 2 Baris Lega) --}}
        <h4 
            style="margin: 0; font-size: 0.775rem; font-weight: 700; color: #0f172a; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;"
            title="{{ $ticket->subject }}"
        >
            {{ $ticket->subject }}
        </h4>

        {{-- Baris 3: Pelapor --}}
        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.6875rem; color: #64748b; margin-top: 0.35rem;">
            <i class="ri-user-3-line" style="color: #94a3b8; font-size: 0.75rem; flex-shrink: 0;"></i>
            <span style="font-weight: 500; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $ticket->user?->name ?? 'Pegawai' }}</span>
        </div>
    </div>

    {{-- Baris 4: Tombol Aksi Lega Tanpa Sempit --}}
    <div style="display: flex; align-items: center; gap: 0.45rem; padding-top: 0.5rem; border-top: 1px solid #f1f5f9;">
        @if(auth()->user()?->isKetuaTim())
            <button
                type="button"
                wire:click="remindItTeam({{ $ticket->id }})"
                style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.35rem 0.65rem; border-radius: 0.45rem; background-color: #d97706; color: #ffffff; font-size: 0.7rem; font-weight: 700; border: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.06); transition: all 0.15s ease;"
                onmouseover="this.style.backgroundColor='#b45309';"
                onmouseout="this.style.backgroundColor='#d97706';"
                title="Kirimkan notifikasi pengingat ke Tim TI untuk tiket ini"
            >
                <i class="ri-notification-3-line" style="font-size: 0.775rem;"></i>
                <span>Ingatkan Tim TI</span>
            </button>
        @else
            <button
                type="button"
                wire:click="claimTicket({{ $ticket->id }})"
                style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.35rem 0.65rem; border-radius: 0.45rem; background-color: #059669; color: #ffffff; font-size: 0.7rem; font-weight: 700; border: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.06); transition: all 0.15s ease;"
                onmouseover="this.style.backgroundColor='#047857';"
                onmouseout="this.style.backgroundColor='#059669';"
                title="Ambil alih aduan ini langsung ke akun Anda"
            >
                <i class="ri-hand-coin-line" style="font-size: 0.775rem;"></i>
                <span>Tangani Sekarang</span>
            </button>
        @endif
        <a
            href="{{ url('/admin/tickets/' . $ticket->id . '/edit') }}"
            style="width: 1.85rem; height: 1.85rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.45rem; background-color: #f8fafc; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; flex-shrink: 0; transition: all 0.15s ease;"
            onmouseover="this.style.backgroundColor='#e2e8f0'; this.style.color='#0f172a';"
            onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#475569';"
            title="Buka rincian tiket di tab editor"
        >
            <i class="ri-external-link-line" style="font-size: 0.775rem;"></i>
        </a>
    </div>
</div>
