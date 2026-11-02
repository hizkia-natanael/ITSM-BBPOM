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
