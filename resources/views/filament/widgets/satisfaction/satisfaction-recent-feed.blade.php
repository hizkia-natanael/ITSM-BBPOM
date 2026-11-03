@props(['surveys'])

@if($surveys->isEmpty())
    <div class="bbpom-survey-empty-card">
        <div class="bbpom-survey-empty-icon">
            <i class="ri-chat-smile-2-line"></i>
        </div>
        <h4 class="bbpom-survey-empty-title">Belum Ada Ulasan Masuk</h4>
        <p class="bbpom-survey-empty-desc">
            Ulasan kepuasan dari pegawai pemohon yang tiketnya telah diselesaikan akan otomatis muncul secara berkala di sini.
        </p>
    </div>
@else
    <div class="bbpom-survey-feed-container">
        @foreach($surveys as $survey)
            @include('filament.widgets.satisfaction.satisfaction-review-item', ['survey' => $survey])
        @endforeach
    </div>
@endif
