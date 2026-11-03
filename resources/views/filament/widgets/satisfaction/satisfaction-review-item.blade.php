@props(['survey'])

<div class="bbpom-survey-item-card">
    <div class="bbpom-survey-item-top">
        <div class="bbpom-survey-item-left">
            <div class="bbpom-survey-avatar">
                {{ strtoupper(substr($survey->user->name ?? 'P', 0, 1)) }}
            </div>
            <div class="bbpom-survey-user-info">
                <h5 class="bbpom-survey-user-name">
                    {{ $survey->user->name ?? 'Pegawai' }}
                </h5>
                <a
                    href="{{ \App\Filament\Resources\Tickets\TicketResource::getUrl('edit', ['record' => $survey->ticket_id]) }}"
                    class="bbpom-survey-ticket-link"
                >
                    {{ $survey->ticket->ticket_number ?? '#TIK' }}
                </a>
            </div>
        </div>

        <div class="bbpom-survey-item-meta">
            <span class="bbpom-survey-rating-badge">
                <i class="ri-star-fill" style="color: #d97706; font-size: 0.65rem;"></i>
                <span>{{ number_format($survey->average_rating, 1) }}</span>
            </span>
            <span class="bbpom-survey-time">
                {{ $survey->created_at->diffForHumans() }}
            </span>
        </div>
    </div>

    @if($survey->feedback)
        <div class="bbpom-survey-feedback-quote">
            "{{ $survey->feedback }}"
        </div>
    @endif

    <div class="bbpom-survey-breakdown-row">
        <span>Kecepatan: <strong style="color: #0f172a;">{{ $survey->speed_rating }}/5</strong></span>
        <span>&bull;</span>
        <span>Kualitas: <strong style="color: #0f172a;">{{ $survey->quality_rating }}/5</strong></span>
        <span>&bull;</span>
        <span>Komunikasi: <strong style="color: #0f172a;">{{ $survey->communication_rating }}/5</strong></span>
        <span>&bull;</span>
        <span>Sistem: <strong style="color: #0f172a;">{{ $survey->system_rating }}/5</strong></span>
    </div>
</div>
