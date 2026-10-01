@props(['description'])

@php
    $isStructured = str_contains($description, '**Layanan:**') || str_contains($description, '### Rincian Kebutuhan');

    $meta = [];
    $fields = [];
    $longFields = [];

    if ($isStructured) {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $description));
        $inRincian = false;
        $currentLongKey = null;
        $currentLongVal = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || $trimmed === '---') {
                continue;
            }

            if (str_starts_with($trimmed, '### Rincian Kebutuhan')) {
                $inRincian = true;
                continue;
            }

            if (!$inRincian) {
                if (preg_match('/^\*\*(.+?):\*\*\s*(.*)$/', $trimmed, $m)) {
                    $meta[trim($m[1])] = trim($m[2]);
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
                        $fields[$key] = $val;
                    }
                } elseif ($currentLongKey !== null) {
                    $currentLongVal[] = $line;
                }
            }
        }

        if ($currentLongKey !== null) {
            $longFields[$currentLongKey] = implode("\n", $currentLongVal);
        }
    }

    $getFieldIcon = function(string $key): string {
        $k = strtolower($key);
        if (str_contains($k, 'user') || str_contains($k, 'nama') || str_contains($k, 'pemohon')) return 'ri-user-3-line';
        if (str_contains($k, 'pass') || str_contains($k, 'sandi')) return 'ri-lock-password-line';
        if (str_contains($k, 'sampel') || str_contains($k, 'kode')) return 'ri-test-tube-line';
        if (str_contains($k, 'email') || str_contains($k, 'mail')) return 'ri-mail-line';
        if (str_contains($k, 'domain') || str_contains($k, 'url') || str_contains($k, 'link') || str_contains($k, 'subsite')) return 'ri-global-line';
        if (str_contains($k, 'ip') || str_contains($k, 'mac') || str_contains($k, 'port') || str_contains($k, 'lan')) return 'ri-router-line';
        if (str_contains($k, 'telepon') || str_contains($k, 'wa') || str_contains($k, 'kontak') || str_contains($k, 'hp')) return 'ri-phone-line';
        if (str_contains($k, 'lokasi') || str_contains($k, 'ruang') || str_contains($k, 'gedung')) return 'ri-map-pin-2-line';
        if (str_contains($k, 'unit') || str_contains($k, 'seksi') || str_contains($k, 'bidang')) return 'ri-building-4-line';
        if (str_contains($k, 'tanggal') || str_contains($k, 'waktu') || str_contains($k, 'deadline') || str_contains($k, 'tenggat')) return 'ri-calendar-event-line';
        if (str_contains($k, 'tipe') || str_contains($k, 'kategori') || str_contains($k, 'jenis')) return 'ri-price-tag-3-line';
        if (str_contains($k, 'orientasi') || str_contains($k, 'ukuran')) return 'ri-aspect-ratio-line';
        if (str_contains($k, 'jabatan') || str_contains($k, 'nip')) return 'ri-id-card-line';
        if (str_contains($k, 'status') || str_contains($k, 'kondisi')) return 'ri-pulse-line';
        return 'ri-information-line';
    };
@endphp

@if($isStructured)
    <div class="space-y-5">
        <!-- Tag Header Layanan & Subkategori -->
        @if(!empty($meta))
            <div class="flex flex-wrap items-center gap-2.5 p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl">
                @if(isset($meta['Layanan']))
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                        <i class="ri-apps-2-line text-emerald-700"></i>
                        <span>Layanan: {{ $meta['Layanan'] }}</span>
                    </div>
                @endif

                @if(isset($meta['Sub Kategori']))
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-xs font-semibold">
                        <i class="ri-price-tag-3-line text-blue-700"></i>
                        <span>Sub Kategori: {{ $meta['Sub Kategori'] }}</span>
                    </div>
                @endif

                @if(isset($meta['Tanggal Pengajuan']))
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white text-slate-600 border border-slate-200 text-xs font-medium ml-auto">
                        <i class="ri-calendar-check-line text-slate-500"></i>
                        <span>{{ $meta['Tanggal Pengajuan'] }} WIB</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- Grid Rincian Formulir Spesifikasi -->
        @if(!empty($fields))
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
                    <i class="ri-list-check-3 text-emerald-700 text-sm"></i>
                    Rincian Kebutuhan & Parameter Formulir:
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($fields as $label => $val)
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-colors">
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

        <!-- Long Text Fields (Catatan Tambahan, Deskripsi Masalah, dll) -->
        @if(!empty($longFields))
            <div class="space-y-3 pt-2">
                @foreach($longFields as $longLabel => $longVal)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mb-2">
                            <i class="ri-file-text-line text-emerald-700"></i>
                            {{ $longLabel }}:
                        </span>
                        <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-white p-3 rounded-lg border border-slate-200/60">
                            {{ $longVal }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@else
    <!-- Tampilan Plain Text Deskripsi Biasa -->
    <div class="rounded-xl bg-slate-50/70 border border-slate-200 p-4 sm:p-5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
            <i class="ri-file-list-2-line text-emerald-700 text-sm"></i>
            Deskripsi Pengaduan / Masalah:
        </h4>
        <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line">
            {{ $description }}
        </div>
    </div>
@endif
