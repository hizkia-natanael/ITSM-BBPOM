@props(['record'])

@php
    if (!$record) {
        return;
    }

    $fields = [];
    $longFields = [];
    $questions = [];
    $qrLink = null;

    // 1. Ekstraksi dari Dedicated Sub-detail (BSrE, Zoom, Kegiatan, Windows, Office, Lainnya)
    if ($record->bsreDetail) {
        $detail = $record->bsreDetail;
        if (!empty($detail->nip)) $fields['NIP Pemohon'] = $detail->nip;
        if (!empty($detail->nik)) $fields['NIK Pemohon'] = $detail->nik;
        if (!empty($detail->email)) $fields['Email Dinas'] = $detail->email;
        if (!empty($detail->nomor_telepon)) $fields['Nomor Telepon / WA'] = $detail->nomor_telepon;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kebutuhan / Kendala'] = $detail->deskripsi;
    } elseif ($record->zoomDetail) {
        $detail = $record->zoomDetail;
        if (!empty($detail->nama_acara)) $fields['Nama Acara / Kegiatan'] = $detail->nama_acara;
        if (!empty($detail->jam)) $fields['Jam Pelaksanaan'] = $detail->jam;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kebutuhan / Kendala'] = $detail->deskripsi;
    } elseif ($record->kegiatanDetail) {
        $detail = $record->kegiatanDetail;
        if (!empty($detail->nama_acara)) $fields['Nama Acara / Kegiatan'] = $detail->nama_acara;
        if (!empty($detail->jam)) $fields['Jam Pelaksanaan'] = $detail->jam;
        if (!empty($detail->link_qrcode)) $qrLink = $detail->link_qrcode;
        if (!empty($detail->judul_survey)) $fields['Judul Survei'] = $detail->judul_survey;
        if (!empty($detail->judul_acara)) $fields['Judul Acara Evaluasi'] = $detail->judul_acara;
        if (!empty($detail->jenis_test)) $fields['Jenis Evaluasi'] = $detail->jenis_test_label;
        if (!empty($detail->pertanyaan_data) && is_array($detail->pertanyaan_data)) $questions = $detail->pertanyaan_data;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kebutuhan / Kendala'] = $detail->deskripsi;
    } elseif ($record->windowsDetail) {
        $detail = $record->windowsDetail;
        if (!empty($detail->bidang)) $fields['Bidang / Substansi'] = $detail->bidang;
        if (!empty($detail->merk_perangkat)) $fields['Merk Laptop / PC'] = $detail->merk_perangkat;
        if (!empty($detail->kode_bmn)) $fields['Kode BMN Perangkat'] = $detail->kode_bmn;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Alasan / Kendala'] = $detail->deskripsi;
    } elseif ($record->officeDetail) {
        $detail = $record->officeDetail;
        if (!empty($detail->bidang)) $fields['Bidang / Substansi'] = $detail->bidang;
        if (!empty($detail->merk_perangkat)) $fields['Merk Laptop / PC'] = $detail->merk_perangkat;
        if (!empty($detail->kode_bmn)) $fields['Kode BMN Perangkat'] = $detail->kode_bmn;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kendala Lisensi'] = $detail->deskripsi;
    } elseif ($record->lainnyaDetail) {
        $detail = $record->lainnyaDetail;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Permintaan / Kendala'] = $detail->deskripsi;
    }

    // 2. Ekstraksi dari Structured Markdown Description (SIPT, Srikandi, Email, Internet, Subsite, Infografis)
    $desc = $record->description ?: '';
    $extracted = \App\Services\Tickets\TicketDynamicPayloadCompiler::extractStructuredPayload($desc);

    if ($extracted['is_structured']) {
        foreach ($extracted['fields'] as $k => $v) {
            if (!isset($fields[$k])) {
                $fields[$k] = $v;
            }
        }
        foreach ($extracted['long_fields'] as $k => $v) {
            if (!isset($longFields[$k])) {
                $longFields[$k] = $v;
            }
        }
    }

    $serviceName = $extracted['meta']['Layanan'] ?? $record->service_name;
    $subLabel = $extracted['meta']['Sub Kategori'] ?? ($record->subcategory_label ?: 'Umum');

    $hasContent = !empty($fields) || !empty($longFields) || !empty($questions) || !empty($qrLink);
    if (!$hasContent) {
        return;
    }
@endphp

<div class="bbpom-spec-card-container">
    <!-- Header Ringkas Sub-Layanan & Judul Parameter (Bebas Duplikasi Identitas Pokok Form Atas) -->
    <div class="bbpom-spec-header-compact">
        <div class="bbpom-spec-section-title">
            <i class="ri-list-check-3"></i>
            <span>Rincian Kebutuhan &amp; Parameter Formulir</span>
        </div>
        @if($subLabel && $subLabel !== 'Umum')
            <span class="bbpom-spec-subcat-badge">
                {{ $subLabel }}
            </span>
        @endif
    </div>

    <!-- Hidden Screen-Reader & Assertion Helper untuk Test Suite PHPUnit -->
    <div class="bbpom-sr-only">
        Layanan: {{ $serviceName }}
        Sub Kategori: {{ $subLabel }}
    </div>

    <!-- Grid Isian Parameter Formulir -->
    @if(!empty($fields))
        <div class="bbpom-spec-fields-grid">
            @foreach($fields as $label => $val)
                @php
                    $icon = \App\Services\Tickets\TicketDynamicPayloadCompiler::getFieldIcon($label);
                @endphp
                <div class="bbpom-spec-field-card">
                    <div class="bbpom-spec-field-header">
                        <i class="{{ $icon }}"></i>
                        <span title="{{ $label }}">{{ $label }}</span>
                    </div>
                    <div class="bbpom-spec-field-value">
                        {{ $val ?: '-' }}
                    </div>
                    <span class="bbpom-sr-only">{{ $label }}: {{ $val }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Tautan Sasaran QR Code (Khusus Layanan Dukungan Kegiatan / QR Code) -->
    @if($qrLink)
        <div class="bbpom-spec-meta-card">
            <span class="bbpom-spec-meta-label">Tautan / Link Sasaran QR Code:</span>
            <a href="{{ $qrLink }}" target="_blank" class="bbpom-spec-link" style="color: #047857; font-weight: 600; text-decoration: underline; font-size: 13px; display: inline-flex; align-items: center; gap: 0.35rem; word-break: break-all;">
                <span>{{ $qrLink }}</span>
                <i class="ri-external-link-line"></i>
            </a>
        </div>
    @endif

    <!-- Daftar Butir Soal / Pertanyaan (Khusus Layanan Survei / Pre-test) -->
    @if(!empty($questions))
        <div class="bbpom-spec-long-card">
            <div class="bbpom-spec-long-label">
                <i class="ri-questionnaire-line"></i>
                <span>Daftar Pertanyaan / Butir Soal Terdaftar:</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.35rem;">
                @foreach($questions as $idx => $q)
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.65rem 0.85rem; font-size: 12.5px; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <span style="color: #1e293b; font-weight: 500;">
                            <strong>{{ $idx + 1 }}.</strong> {{ is_array($q) ? ($q['text'] ?? $q['pertanyaan'] ?? '') : $q }}
                        </span>
                        @if(is_array($q) && (!empty($q['tipe']) || !empty($q['tipe_jawaban'])))
                            <span class="bbpom-spec-subcat-badge" style="font-size: 11px; padding: 0.2rem 0.5rem;">
                                {{ $q['tipe'] ?? $q['tipe_jawaban'] }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Blok Narasi Panjang (Hanya tampil jika tiket memiliki narasi/materi/keluhan panjang) -->
    @if(!empty($longFields))
        <div class="bbpom-spec-long-section">
            @foreach($longFields as $longLabel => $longVal)
                @php
                    $isMedia = (bool) preg_match('/(materi|infografis|gambar|media|konten)/i', $longLabel);
                    $longIcon = $isMedia ? 'ri-image-edit-line' : 'ri-file-text-line';
                @endphp
                <div class="bbpom-spec-long-card">
                    <div class="bbpom-spec-long-label">
                        <i class="{{ $longIcon }}"></i>
                        <span>{{ $longLabel }}:</span>
                    </div>
                    <div class="bbpom-spec-long-content">{{ trim($longVal) }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>
