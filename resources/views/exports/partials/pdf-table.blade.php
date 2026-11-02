<!-- DATA TABLE ADUAN -->
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 3%;">No</th>
            <th style="width: 11%;">No. Tiket</th>
            <th style="width: 9%;">Waktu Masuk</th>
            <th style="width: 13%;">Pelapor / NIP</th>
            <th style="width: 13%;">Layanan / Sub-Kategori</th>
            <th style="width: 18%;">Subjek Kendala</th>
            <th style="width: 16%;">Deskripsi Singkat</th>
            <th style="width: 9%;">Petugas IT</th>
            <th style="width: 8%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tickets as $index => $ticket)
            <tr>
                <td class="col-center">{{ $index + 1 }}</td>
                <td class="col-ticket-no col-center">{{ $ticket->ticket_number }}</td>
                <td class="col-center">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <strong>{{ $ticket->user?->name ?? 'Anonim' }}</strong><br>
                    <span style="color: #64748b; font-size: 6.8pt;">{{ $ticket->user?->email ?? '-' }}</span>
                    @if($ticket->user?->nip)
                        <br><span style="color: #64748b; font-size: 6.8pt;">NIP: {{ $ticket->user->nip }}</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $ticket->service_name }}</strong><br>
                    <span style="color: #0369a1; font-size: 6.8pt;">
                        {{ $ticket->subcategory_label ?? '-' }}
                    </span>
                </td>
                <td>{{ $ticket->clean_subject ?? \App\Services\Export\TicketQueryBuilder::stripMarkdown($ticket->subject) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($ticket->clean_description ?? \App\Services\Export\TicketQueryBuilder::stripMarkdown($ticket->description), 110) }}</td>
                <td>{{ $ticket->handledBy?->name ?? 'Belum Ditugaskan' }}</td>
                <td class="col-center">
                    <span class="badge badge-{{ $ticket->status }}">
                        {{ $ticket->status_label }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="col-center" style="padding: 18px 10px; color: #64748b; font-style: italic;">
                    Tidak ada data pengaduan yang sesuai dengan kriteria filter yang dipilih.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
