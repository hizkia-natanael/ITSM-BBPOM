<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi Aduan ITSM - BBPOM di Semarang</title>
    @include('exports.partials.pdf-styles')
</head>
<body>

    @if(!empty($kopFooterBase64))
        <!-- Fixed Official Footer Wave BBPOM Semarang across all pages -->
        <footer>
            <img src="data:image/jpeg;base64,{{ $kopFooterBase64 }}" class="footer-wave-img" alt="Footer Wave BBPOM Semarang">
        </footer>
    @endif

    <!-- KOP Surat Resmi & Metadata Strip -->
    @include('exports.partials.pdf-kop-header')

    <!-- Executive KPI Strip -->
    @include('exports.partials.pdf-kpi')

    <!-- Tabel Data Aduan -->
    @include('exports.partials.pdf-table')

    <!-- Lembar Pengesahan & Audit Footer -->
    @include('exports.partials.pdf-signatures')

</body>
</html>
