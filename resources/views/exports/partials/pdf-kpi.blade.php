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
