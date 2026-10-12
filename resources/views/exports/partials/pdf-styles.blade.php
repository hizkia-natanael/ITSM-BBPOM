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

    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
    .kop-logo { width: 72px; vertical-align: middle; text-align: left; padding-left: 2px; padding-bottom: 2px; }
    .kop-logo img { width: 64px; height: auto; display: block; }
    .kop-text { vertical-align: middle; text-align: center; padding-right: 70px; }

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
    .kop-blue { color: #014282; }
    .kop-green { color: #04A651; }
    .kop-alamat-line, .kop-kontak-line, .kop-web-line {
        font-size: 8.5pt;
        font-weight: 400;
        color: #181C18;
        line-height: 1.35;
        margin: 1px 0 0 0;
        font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
    }

    .kop-divider-thick { height: 2.5px; background-color: #1261B0; margin-top: 5px; margin-bottom: 1.5px; }
    .kop-divider-thin { height: 0.8px; background-color: #1261B0; margin-bottom: 10px; }

    .meta-strip-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 9px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
    }
    .meta-strip-table td { padding: 5px 8px; font-size: 7.8pt; vertical-align: top; }
    .meta-label { font-weight: 700; color: #475569; width: 13%; }
    .meta-val { color: #0f172a; font-weight: 600; width: 20%; }

    .kpi-table { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 11px; }
    .kpi-card { background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 5px 7px; text-align: center; vertical-align: middle; }
    .kpi-title { font-size: 6.8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #475569; margin-bottom: 2px; }
    .kpi-number { font-size: 11.5pt; font-weight: 800; color: #0f172a; }
    .kpi-card.kpi-selesai { background-color: #f0fdf4; border-color: #86efac; }
    .kpi-card.kpi-selesai .kpi-number { color: #15803d; }
    .kpi-card.kpi-diproses { background-color: #f0f9ff; border-color: #7dd3fc; }
    .kpi-card.kpi-diproses .kpi-number { color: #0284c7; }
    .kpi-card.kpi-pending { background-color: #fffbeb; border-color: #fcd34d; }
    .kpi-card.kpi-pending .kpi-number { color: #b45309; }
    .kpi-card.kpi-ditolak { background-color: #fff1f2; border-color: #fca5a5; }
    .kpi-card.kpi-ditolak .kpi-number { color: #e11d48; }

    .data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
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
    .data-table td { font-size: 7.3pt; padding: 5px 4px; border: 1px solid #cbd5e1; vertical-align: top; color: #1e293b; word-wrap: break-word; }
    .data-table tr:nth-child(even) { background-color: #f8fafc; }
    .col-center { text-align: center; }
    .col-ticket-no { font-weight: 700; color: #0369a1; white-space: nowrap; }

    .badge { display: inline-block; padding: 2px 5px; font-size: 6.8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; border-radius: 3px; text-align: center; }
    .badge-selesai { background-color: #dcfce7; color: #15803d; border: 0.5px solid #86efac; }
    .badge-diproses { background-color: #e0f2fe; color: #0369a1; border: 0.5px solid #7dd3fc; }
    .badge-pending { background-color: #fef3c7; color: #b45309; border: 0.5px solid #fcd34d; }
    .badge-ditolak { background-color: #ffe4e6; color: #be123c; border: 0.5px solid #fca5a5; }

    .signature-table { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid; }
    .signature-table td { vertical-align: top; font-size: 8pt; }
    .signature-city { margin-bottom: 4px; font-size: 8pt; color: #334155; }
    .signature-role { font-weight: 700; color: #0f172a; margin-bottom: 75px; }
    .signature-name { font-weight: 800; color: #0f172a; text-decoration: underline; }
    .signature-nip { font-size: 7.5pt; color: #64748b; }

    .footer-stamp { margin-top: 15px; font-size: 6.8pt; color: #94a3b8; border-top: 0.5px dashed #cbd5e1; padding-top: 4px; page-break-inside: avoid; }
</style>
