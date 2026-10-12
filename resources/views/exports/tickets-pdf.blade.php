<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi Aduan ITSM - BBPOM di Semarang</title>
    <style>
        @page {
            margin: 8mm 12mm 16mm 12mm;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 8.5pt;
            line-height: 1.35;
            background-color: #ffffff;
        }

        /* Fixed Footer Wave Background */
        footer {
            position: fixed;
            bottom: -16mm;
            left: -12mm;
            right: -12mm;
            height: 14mm;
        }
        .footer-wave-img {
            width: 100%;
            height: 14mm;
            display: block;
        }

        /* Kop Surat Resmi Dokumen BBPOM di Semarang (Native Vector & Typography) */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }
        .kop-logo {
            width: 72px;
            vertical-align: middle;
            text-align: left;
            padding-left: 2px;
            padding-bottom: 2px;
        }
        .kop-logo img {
            width: 64px;
            height: auto;
            display: block;
        }
        .kop-text {
            vertical-align: middle;
            text-align: center;
            padding-right: 70px; /* Offset logo width for perfect optical centering */
        }
        @font-face {
            font-family: 'Trebuchet MS';
            src: url('{{ str_replace('\\', '/', public_path('fonts/trebucbd.ttf')) }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Arial Narrow';
            src: url('{{ str_replace('\\', '/', public_path('fonts/ARIALN.TTF')) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .kop-instansi-title {
            font-size: 13.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.25;
            margin: 0 0 3px 0;
            font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
        }
        .kop-blue {
            color: #014282;
        }
        .kop-green {
            color: #04A651;
        }
        .kop-alamat-line {
            font-size: 8.5pt;
            font-weight: 400;
            color: #181C18;
            line-height: 1.35;
            margin: 0;
            font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
        }
        .kop-kontak-line {
            font-size: 8.5pt;
            font-weight: 400;
            color: #181C18;
            line-height: 1.35;
            margin: 1px 0 0 0;
            font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
        }
        .kop-web-line {
            font-size: 8.5pt;
            font-weight: 400;
            color: #181C18;
            line-height: 1.35;
            margin: 1px 0 0 0;
            font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
        }

        .kop-divider-thick {
            height: 2.5px;
            background-color: #1261B0;
            margin-top: 5px;
            margin-bottom: 1.5px;
        }
        .kop-divider-thin {
            height: 0.8px;
            background-color: #1261B0;
            margin-bottom: 10px;
        }

        /* Metadata & KPI Strip */
        .meta-strip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 9px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .meta-strip-table td {
            padding: 5px 8px;
            font-size: 7.8pt;
            vertical-align: top;
        }
        .meta-label {
            font-weight: 700;
            color: #475569;
            width: 13%;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 600;
            width: 20%;
        }

        /* KPI Ribbon */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin-bottom: 11px;
        }
        .kpi-card {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            text-align: center;
            vertical-align: middle;
        }
        .kpi-title {
            font-size: 6.8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #475569;
            margin-bottom: 2px;
        }
        .kpi-number {
            font-size: 11.5pt;
            font-weight: 800;
            color: #0f172a;
        }
        .kpi-card.kpi-selesai {
            background-color: #f0fdf4;
            border-color: #86efac;
        }
        .kpi-card.kpi-selesai .kpi-number { color: #15803d; }
        .kpi-card.kpi-diproses {
            background-color: #f0f9ff;
            border-color: #7dd3fc;
        }
        .kpi-card.kpi-diproses .kpi-number { color: #0284c7; }
        .kpi-card.kpi-pending {
            background-color: #fffbeb;
            border-color: #fcd34d;
        }
        .kpi-card.kpi-pending .kpi-number { color: #b45309; }
        .kpi-card.kpi-ditolak {
            background-color: #fff1f2;
            border-color: #fca5a5;
        }
        .kpi-card.kpi-ditolak .kpi-number { color: #e11d48; }
        .kpi-card.kpi-sla {
            background-color: #ecfdf5;
            border-color: #6ee7b7;
        }
        .kpi-card.kpi-sla .kpi-number { color: #065f46; }

        /* Tabel Data Aduan */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #064e3b;
            color: #ffffff;
            font-size: 7.2pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 6px 4px;
            border: 1px solid #04382a;
            text-align: center;
        }
        .data-table td {
            font-size: 7.3pt;
            padding: 5px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            color: #1e293b;
            word-wrap: break-word;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .col-center { text-align: center; }
        .col-ticket-no {
            font-weight: 700;
            color: #0369a1;
            white-space: nowrap;
        }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6.8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-radius: 3px;
            text-align: center;
        }
        .badge-selesai {
            background-color: #dcfce7;
            color: #15803d;
            border: 0.5px solid #86efac;
        }
        .badge-diproses {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 0.5px solid #7dd3fc;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #b45309;
            border: 0.5px solid #fcd34d;
        }
        .badge-ditolak {
            background-color: #ffe4e6;
            color: #be123c;
            border: 0.5px solid #fca5a5;
        }

        /* Pengesahan / Signature */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        .signature-table td {
            vertical-align: top;
            font-size: 8pt;
        }
        .signature-city {
            margin-bottom: 4px;
            font-size: 8pt;
            color: #334155;
        }
        .signature-role {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 75px;
        }
        .signature-name {
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
        }
        .signature-nip {
            font-size: 7.5pt;
            color: #64748b;
        }

        /* Footer Audit Stamp */
        .footer-stamp {
            margin-top: 15px;
            font-size: 6.8pt;
            color: #94a3b8;
            border-top: 0.5px dashed #cbd5e1;
            padding-top: 4px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    @if(!empty($kopFooterBase64))
        <!-- Fixed Official Footer Wave BBPOM Semarang across all pages -->
        <footer>
            <img src="data:image/jpeg;base64,{{ $kopFooterBase64 }}" class="footer-wave-img" alt="Footer Wave BBPOM Semarang">
        </footer>
    @endif

    <!-- KOP RESMI DOKUMEN BBPOM DI SEMARANG (NATIVE VECTOR TEXT & LOGO) -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if(!empty($logoBase64))
                    <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo Badan POM">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-instansi-title">
                    <span class="kop-blue">BALAI BESAR </span><span class="kop-green">PENGAWAS OBAT DAN MAKANAN DI SEMARANG</span>
                </div>
                <div class="kop-alamat-line">Jl. Sukun Raya No. 41A Banyumanik Semarang 50264</div>
                <div class="kop-kontak-line">Telp. 024 - 7612324 (ULPK), Fax : 024-7613633</div>
                <div class="kop-web-line">Email : bpom_semarang@pom.go.id &nbsp;;&nbsp; Website : www.pom.go.id</div>
            </td>
        </tr>
    </table>

    <div class="kop-divider-thick"></div>
    <div class="kop-divider-thin"></div>

    <!-- METADATA AUDIT & FILTER -->
    <table class="meta-strip-table">
        <tr>
            <td class="meta-label">Kategori Layanan:</td>
            <td class="meta-val">{{ $summary['category_name'] }}</td>
            <td class="meta-label">Periode Waktu:</td>
            <td class="meta-val">{{ $summary['period_label'] }}</td>
            <td class="meta-label">Filter Status:</td>
            <td class="meta-val">{{ $summary['status_label'] }}</td>
        </tr>
        <tr>
            <td class="meta-label">Petugas IT:</td>
            <td class="meta-val">{{ $summary['handled_by_label'] ?? 'Semua Petugas IT' }}</td>
            <td class="meta-label">Waktu Ekspor:</td>
            <td class="meta-val">{{ $summary['generated_at'] }}</td>
            <td class="meta-label">Petugas Admin:</td>
            <td class="meta-val">{{ $summary['generated_by'] }}</td>
        </tr>
    </table>

    <!-- EXECUTIVE KPI STRIP -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-title">Total Aduan Masuk</div>
                <div class="kpi-number">{{ $summary['total'] }}</div>
            </td>
            <td class="kpi-card kpi-selesai" style="width: 20%;">
                <div class="kpi-title">Tuntas / Selesai</div>
                <div class="kpi-number">{{ $summary['selesai'] }}</div>
            </td>
            <td class="kpi-card kpi-diproses" style="width: 20%;">
                <div class="kpi-title">Sedang Diproses</div>
                <div class="kpi-number">{{ $summary['diproses'] }}</div>
            </td>
            <td class="kpi-card kpi-pending" style="width: 20%;">
                <div class="kpi-title">Menunggu Antrian</div>
                <div class="kpi-number">{{ $summary['pending'] }}</div>
            </td>
            <td class="kpi-card kpi-ditolak" style="width: 20%;">
                <div class="kpi-title">Ditolak / Batal</div>
                <div class="kpi-number">{{ $summary['ditolak'] }}</div>
            </td>
        </tr>
    </table>

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

    <!-- LEMBAR PENGESAHAN DOKUMEN -->
    <table class="signature-table">
        <tr>
            <td style="width: 58%;">
                <!-- Ruang kosong kiri agar pengesahan tunggal berada di sebelah kanan dokumen -->
            </td>
            <td style="width: 42%; text-align: left; padding-left: 20px;">
                <div class="signature-city">Semarang, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="signature-role">Petugas Pengelola Helpdesk TIK,</div>
                <div class="signature-name">{{ $summary['generated_by'] }}</div>
                <div class="signature-nip">
                    @if(!empty($summary['generated_by_nip']))
                        NIP. {{ $summary['generated_by_nip'] }}<br>
                    @endif
                    Administrator Sistem Layanan TI BBPOM di Semarang
                </div>
            </td>
        </tr>
    </table>

    <!-- AUDIT TRACE FOOTER -->
    <div class="footer-stamp">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">
                    Dokumen ini digenerate secara otomatis oleh Sistem Manajemen Layanan TI (ITSM) BBPOM Semarang.
                </td>
                <td style="text-align: right;">
                    Rekapitulasi Resmi Layanan &bull; Confidential
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
