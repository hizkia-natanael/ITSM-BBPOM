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
