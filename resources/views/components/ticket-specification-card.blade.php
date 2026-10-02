@props(['ticket'])

@php
    $fields = [];
    $longFields = [];
    $questions = [];
    $qrLink = null;
    $tanggalPengajuan = null;
    $namaPemohon = null;

    // 1. Ekstraksi dari Dedicated Sub-detail (jika ada)
    if ($ticket->bsreDetail) {
        $detail = $ticket->bsreDetail;
        $tanggalPengajuan = $detail->tanggal_pengajuan ? $detail->tanggal_pengajuan->translatedFormat('d F Y') : null;
        $namaPemohon = $detail->nama;
        if (!empty($detail->nip)) $fields['NIP Pemohon'] = $detail->nip;
        if (!empty($detail->nik)) $fields['NIK Pemohon'] = $detail->nik;
        if (!empty($detail->email)) $fields['Email Dinas'] = $detail->email;
        if (!empty($detail->nomor_telepon)) $fields['Nomor Telepon / WA'] = $detail->nomor_telepon;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kebutuhan / Kendala'] = $detail->deskripsi;
    } elseif ($ticket->zoomDetail) {
        $detail = $ticket->zoomDetail;
        $tanggalPengajuan = $detail->tanggal_pengajuan ? $detail->tanggal_pengajuan->translatedFormat('d F Y') : null;
        $namaPemohon = $detail->nama;
        if (!empty($detail->nama_acara)) $fields['Nama Acara / Kegiatan'] = $detail->nama_acara;
        if (!empty($detail->jam)) $fields['Jam Pelaksanaan'] = $detail->jam;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kebutuhan / Kendala'] = $detail->deskripsi;
    } elseif ($ticket->kegiatanDetail) {
        $detail = $ticket->kegiatanDetail;
        $tanggalPengajuan = $detail->tanggal_pengajuan ? $detail->tanggal_pengajuan->translatedFormat('d F Y') : null;
        $namaPemohon = $detail->nama;
        if (!empty($detail->nama_acara)) $fields['Nama Acara / Kegiatan'] = $detail->nama_acara;
        if (!empty($detail->jam)) $fields['Jam Pelaksanaan'] = $detail->jam;
        if (!empty($detail->link_qrcode)) $qrLink = $detail->link_qrcode;
        if (!empty($detail->judul_survey)) $fields['Judul Survei'] = $detail->judul_survey;
        if (!empty($detail->judul_acara)) $fields['Judul Acara Evaluasi'] = $detail->judul_acara;
        if (!empty($detail->jenis_test)) $fields['Jenis Evaluasi'] = $detail->jenis_test_label;
        if (!empty($detail->pertanyaan_data) && is_array($detail->pertanyaan_data)) $questions = $detail->pertanyaan_data;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kebutuhan / Kendala'] = $detail->deskripsi;
    } elseif ($ticket->windowsDetail) {
        $detail = $ticket->windowsDetail;
        $tanggalPengajuan = $detail->tanggal_pengajuan ? $detail->tanggal_pengajuan->translatedFormat('d F Y') : null;
        $namaPemohon = $detail->nama;
        if (!empty($detail->bidang)) $fields['Bidang / Substansi'] = $detail->bidang;
        if (!empty($detail->merk_perangkat)) $fields['Merk Laptop / PC'] = $detail->merk_perangkat;
        if (!empty($detail->kode_bmn)) $fields['Kode BMN Perangkat'] = $detail->kode_bmn;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Alasan / Kendala'] = $detail->deskripsi;
    } elseif ($ticket->officeDetail) {
        $detail = $ticket->officeDetail;
        $tanggalPengajuan = $detail->tanggal_pengajuan ? $detail->tanggal_pengajuan->translatedFormat('d F Y') : null;
        $namaPemohon = $detail->nama;
        if (!empty($detail->bidang)) $fields['Bidang / Substansi'] = $detail->bidang;
        if (!empty($detail->merk_perangkat)) $fields['Merk Laptop / PC'] = $detail->merk_perangkat;
        if (!empty($detail->kode_bmn)) $fields['Kode BMN Perangkat'] = $detail->kode_bmn;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Kendala Lisensi'] = $detail->deskripsi;
    } elseif ($ticket->lainnyaDetail) {
        $detail = $ticket->lainnyaDetail;
        $tanggalPengajuan = $detail->tanggal_pengajuan ? $detail->tanggal_pengajuan->translatedFormat('d F Y') : null;
        $namaPemohon = $detail->nama;
        if (!empty($detail->deskripsi)) $longFields['Deskripsi Permintaan / Kendala'] = $detail->deskripsi;
    }

    // 2. Ekstraksi dari Structured Markdown Description (untuk SIPT, Srikandi, Email, Internet, Subsite, Infografis, dll)
    $desc = $ticket->description ?: '';
    $isStructured = str_contains($desc, '**Layanan:**') || str_contains($desc, '### Rincian Kebutuhan');

    if ($isStructured) {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $desc));
        $inRincian = false;
        $currentLongKey = null;
        $currentLongVal = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || $trimmed === '---') continue;

            if (str_starts_with($trimmed, '### Rincian Kebutuhan')) {
                $inRincian = true;
                continue;
            }

            if (!$inRincian) {
                if (preg_match('/^\*\*(.+?):\*\*\s*(.*)$/', $trimmed, $m)) {
                    $k = trim($m[1]);
                    $v = trim($m[2]);
                    if ($k === 'Tanggal Pengajuan' && empty($tanggalPengajuan)) $tanggalPengajuan = $v . ' WIB';
                    if ($k === 'Nama Pemohon' && empty($namaPemohon)) $namaPemohon = $v;
                }
            } else {
                if (preg_match('/^(\*|-)?\s*\*\*(.+?):\*\*\s*(.*)$/', $trimmed, $m)) {
                    if ($currentLongKey !== null) {
                        $longFields[$currentLongKey] = implode("\n", $currentLongVal);
                        $currentLongKey = null;
                        $currentLongVal = [];
                    }

                    $key = trim($m[2]);
                    $val = trim($m[3]);

                    $isLong = strlen($val) > 80 || in_array(strtolower($key), [
                        'deskripsi', 'deskripsi masalah', 'keterangan masalah', 'catatan tambahan',
                        'detail permintaan', 'teks / konten infografis', 'isi pesan', 'keterangan'
                    ]);

                    if ($isLong) {
                        $currentLongKey = $key;
                        $currentLongVal = [$val];
                    } else {
                        // Jangan timpa jika sudah ada dari subdetail
                        if (!isset($fields[$key])) {
                            $fields[$key] = $val;
                        }
                    }
                } elseif ($currentLongKey !== null) {
                    $currentLongVal[] = $line;
                }
            }
        }

        if ($currentLongKey !== null && !isset($longFields[$currentLongKey])) {
            $longFields[$currentLongKey] = implode("\n", $currentLongVal);
        }
    } elseif (empty($longFields) && !empty($desc)) {
        // Fallback plain description jika bukan terstruktur
        $longFields['Deskripsi Pengaduan / Masalah'] = $desc;
    }

    // Default Fallback
    if (empty($tanggalPengajuan)) {
        $tanggalPengajuan = $ticket->created_at ? $ticket->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-';
    }
    if (empty($namaPemohon)) {
        $namaPemohon = $ticket->user ? $ticket->user->name : '-';
    }

    $getFieldIcon = function(string $key): string {
        $k = strtolower($key);
        if (str_contains($k, 'user') || str_contains($k, 'nama') || str_contains($k, 'pemohon')) return 'ri-user-3-line';
        if (str_contains($k, 'pass') || str_contains($k, 'sandi')) return 'ri-lock-password-line';
        if (str_contains($k, 'sampel') || str_contains($k, 'kode')) return 'ri-test-tube-line';
        if (str_contains($k, 'email') || str_contains($k, 'mail')) return 'ri-mail-line';
        if (str_contains($k, 'domain') || str_contains($k, 'url') || str_contains($k, 'link') || str_contains($k, 'subsite')) return 'ri-global-line';
        if (str_contains($k, 'ip') || str_contains($k, 'mac') || str_contains($k, 'port') || str_contains($k, 'lan') || str_contains($k, 'wifi')) return 'ri-router-line';
        if (str_contains($k, 'telepon') || str_contains($k, 'wa') || str_contains($k, 'kontak') || str_contains($k, 'hp')) return 'ri-phone-line';
        if (str_contains($k, 'lokasi') || str_contains($k, 'ruang') || str_contains($k, 'gedung')) return 'ri-map-pin-2-line';
        if (str_contains($k, 'unit') || str_contains($k, 'seksi') || str_contains($k, 'bidang')) return 'ri-building-4-line';
        if (str_contains($k, 'acara') || str_contains($k, 'kegiatan') || str_contains($k, 'rapat')) return 'ri-calendar-event-line';
        if (str_contains($k, 'jam') || str_contains($k, 'waktu') || str_contains($k, 'tanggal') || str_contains($k, 'deadline') || str_contains($k, 'tenggat')) return 'ri-time-line';
        if (str_contains($k, 'tipe') || str_contains($k, 'kategori') || str_contains($k, 'jenis')) return 'ri-price-tag-3-line';
        if (str_contains($k, 'perangkat') || str_contains($k, 'laptop') || str_contains($k, 'komputer') || str_contains($k, 'pc') || str_contains($k, 'merk')) return 'ri-computer-line';
        if (str_contains($k, 'bmn')) return 'ri-barcode-line';
        if (str_contains($k, 'nip') || str_contains($k, 'nik') || str_contains($k, 'jabatan')) return 'ri-id-card-line';
        if (str_contains($k, 'status') || str_contains($k, 'kondisi')) return 'ri-pulse-line';
        return 'ri-information-line';
    };
@endphp

<!-- Unified Enterprise Specification Card (12 Katalog Layanan Terpadu) -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 relative overflow-hidden space-y-6">
    <!-- Header Kartu Seragam -->
    <div class="border-b border-slate-100 pb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0">
                <i class="{{ $ticket->service_icon }}"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">
                    {{ $ticket->service_badge ?: 'Katalog Layanan TIK' }}
                </span>
                <h3 class="text-base font-extrabold text-slate-900 mt-0.5">
                    {{ $ticket->service_name ?: $ticket->category->name }}
                </h3>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60 shrink-0">
            {{ $ticket->subcategory_label ?: 'Katalog TI' }}
        </span>
    </div>

    <!-- Baris Identitas & Tanggal Pengajuan (Grid 2 Kolom Seragam) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
            <span class="text-slate-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
            <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                {{ $tanggalPengajuan }}
            </span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
            <span class="text-slate-400 block text-[11px] font-medium">Nama Pemohon:</span>
            <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                {{ $namaPemohon }}
            </span>
        </div>
    </div>

    <!-- Grid Isian Spesifikasi Parameter Formulir -->
    @if(!empty($fields))
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
                <i class="ri-list-check-3 text-emerald-700 text-sm"></i>
                Rincian Kebutuhan & Parameter Formulir:
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                @foreach($fields as $label => $val)
                    <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-2xs hover:border-emerald-300 transition-colors">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="{{ $getFieldIcon($label) }} text-slate-400 text-sm"></i>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 truncate" title="{{ $label }}">
                                {{ $label }}
                            </span>
                        </div>
                        <div class="text-xs font-semibold text-slate-900 break-words font-mono bg-slate-50/80 px-2.5 py-1.5 rounded-lg border border-slate-100">
                            {{ $val ?: '-' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Tautan Sasaran QR Code (Khusus Layanan QR Code jika ada) -->
    @if($qrLink)
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
            <span class="text-slate-400 block text-[11px] font-medium mb-1">Tautan / Link Sasaran QR Code:</span>
            <a href="{{ $qrLink }}" target="_blank" class="font-mono text-emerald-700 hover:text-emerald-800 underline text-xs break-all flex items-center gap-1.5">
                <span>{{ $qrLink }}</span>
                <i class="ri-external-link-line"></i>
            </a>
        </div>
    @endif

    <!-- Daftar Butir Pertanyaan / Soal Evaluasi (Khusus Layanan Survei / Pretest jika ada) -->
    @if(!empty($questions))
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
            <span class="text-slate-500 block text-xs font-bold uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                <i class="ri-questionnaire-line text-emerald-700"></i>
                Daftar Pertanyaan / Butir Soal Terdaftar:
            </span>
            <div class="space-y-2">
                @foreach($questions as $idx => $q)
                    <div class="p-2.5 bg-white rounded-lg border border-slate-200 flex items-center justify-between gap-3 text-xs shadow-2xs">
                        <span class="font-medium text-slate-800">
                            <strong>{{ $idx + 1 }}.</strong> {{ is_array($q) ? ($q['text'] ?? $q['pertanyaan'] ?? '') : $q }}
                        </span>
                        @if(is_array($q) && (!empty($q['tipe']) || !empty($q['tipe_jawaban'])))
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 shrink-0">
                                {{ $q['tipe'] ?? $q['tipe_jawaban'] }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Kolom Teks Panjang (Deskripsi Kendala, Catatan Tambahan, dll.) -->
    @if(!empty($longFields))
        <div class="space-y-3 pt-1">
            @foreach($longFields as $longLabel => $longVal)
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mb-2">
                        <i class="ri-file-text-line text-emerald-700"></i>
                        {{ $longLabel }}:
                    </span>
                    <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-white p-3.5 rounded-lg border border-slate-200/70">
                        {{ trim($longVal) }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Komponen Lampiran Berkas Terpadu -->
    <x-ticket-attachments-list :ticket="$ticket" />
</div>
