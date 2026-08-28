@extends('layouts.app')
@section('title', 'Unduh Laporan')

@section('content')
<div id="laporanContent">
<div class="panel">
  <div class="panel-head"><h3>Ringkasan Eksekutif</h3><div class="sub">Periode: Agustus 2026 · sumber: data performa mesin &amp; riwayat maintenance SIPPM</div></div>
  <div class="panel-body" style="padding:6px 18px 14px;">
    <table data-sortable>
      <thead><tr><th>Mesin</th><th>Bagian</th><th>OEE (%)</th><th>Availability (%)</th><th>Downtime Bulan Ini (jam)</th><th>Perbaikan Bulan Ini</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($mesinList as $m)
          <tr>
            <td>{{ $m->nama }}</td>
            <td>{{ $m->bagian }}</td>
            <td>{{ $m->oee }}</td>
            <td>{{ $m->availability }}</td>
            <td>{{ $m->downtime_bulan_ini_jam }}</td>
            <td>{{ $m->jumlah_perbaikan_bulan_ini }}</td>
            <td><span class="badge {{ $m->statusBadgeClass() }}">{{ $m->status }}</span></td>
          </tr>
        @empty
          <tr><td colspan="7" style="color:var(--ink-soft);">Belum ada data mesin.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Riwayat Maintenance Terbaru</h3><div class="sub">{{ $riwayat->count() }} laporan perbaikan</div></div>
  <div class="panel-body" style="padding:6px 18px 14px;">
    <table>
      <thead><tr><th>No. Laporan</th><th>Tanggal</th><th>Mesin</th><th>Kategori</th><th>Komponen Diganti</th><th>Teknisi</th><th>Downtime (menit)</th></tr></thead>
      <tbody>
        @forelse($riwayat->take(10) as $r)
          <tr>
            <td>{{ $r->no_laporan }}</td>
            <td>{{ optional($r->diselesaikan_pada)->format('d M Y') }}</td>
            <td>{{ $r->mesin->nama ?? '-' }}</td>
            <td><span class="badge {{ $r->kategoriBadgeClass() }}">{{ $r->kategori }}</span></td>
            <td>{{ $r->komponen_diganti ?? '-' }}</td>
            <td>{{ $r->teknisi_nama }}</td>
            <td>{{ $r->downtime_menit }}</td>
          </tr>
        @empty
          <tr><td colspan="7" style="color:var(--ink-soft);">Belum ada riwayat maintenance.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Unduh Laporan Eksekutif</h3></div>
  <div class="panel-body">
    <div class="form-grid" style="margin-bottom:16px;">
      <div class="field"><label>Periode</label><select><option>Agustus 2026</option><option>Kuartal 3 2026</option><option>Tahun 2026</option></select></div>
      <div class="field"><label>Cakupan</label><select><option>Seluruh Mesin</option><option>Per Mesin</option></select></div>
    </div>
    <div class="export-box">
      <div class="export-opt" role="button" tabindex="0" onclick="exportLaporanPdf('laporanContent', 'Laporan-Eksekutif-SIMPM.pdf')"><div class="eo-ic">📄</div><div class="eo-title">Ringkasan Eksekutif (PDF)</div><div class="eo-sub">OEE, availability, downtime &amp; ranking mesin</div></div>
      <div class="export-opt" role="button" tabindex="0" onclick="exportLaporanExcel()"><div class="eo-ic">📊</div><div class="eo-title">Data Lengkap (Excel)</div><div class="eo-sub">Seluruh riwayat maintenance periode terpilih</div></div>
    </div>
  </div>
</div>
@endsection

@push('lib-scripts')
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
@endpush

@section('scripts')
<script>
  @php
    $riwayatRows = $riwayat->map(fn ($r) => [
      $r->no_laporan,
      optional($r->diselesaikan_pada)->format('Y-m-d'),
      $r->mesin->nama ?? '-',
      $r->kategori,
      $r->komponen_diganti,
      $r->tindakan,
      $r->teknisi_nama,
      $r->downtime_menit,
    ]);
  @endphp

  function exportLaporanExcel() {
    const mesin = @json($mesinList);
    const riwayat = @json($riwayatRows);

    const rowsMesin = [['Mesin', 'Bagian', 'OEE (%)', 'Availability (%)', 'MTTR (jam)', 'MTBF (jam)', 'Downtime Bulan Ini (jam)', 'Perbaikan Bulan Ini', 'Status']];
    mesin.forEach(m => rowsMesin.push([m.nama, m.bagian, m.oee, m.availability, m.mttr_jam, m.mtbf_jam, m.downtime_bulan_ini_jam, m.jumlah_perbaikan_bulan_ini, m.status]));

    const rowsRiwayat = [['No. Laporan', 'Tanggal Selesai', 'Mesin', 'Kategori', 'Komponen Diganti', 'Tindakan', 'Teknisi', 'Downtime (menit)']];
    riwayat.forEach(r => rowsRiwayat.push(r));

    exportSheetsToExcel('Data-Lengkap-SIMPM-Manajer.xlsx', [
      { name: 'Ringkasan Mesin', rows: rowsMesin },
      { name: 'Riwayat Maintenance', rows: rowsRiwayat },
    ]);
  }
</script>
@endsection
