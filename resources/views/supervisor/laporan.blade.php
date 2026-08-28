@extends('layouts.app')
@section('title', 'Laporan & Grafik')

@section('content')
<div id="laporanContent">
<div class="filter-row">
  <select class="fsel"><option>Periode: Mei – Agustus 2026</option><option>Kuartal Berjalan</option><option>Tahun Berjalan</option></select>
</div>

<div class="panel chart-panel">
  <div class="panel-head"><div><h3>Jumlah Perbaikan per Kategori per Bulan</h3><div class="sub">Diambil dari riwayat maintenance yang dikirim Teknisi di SIPPM</div></div></div>
  <div class="panel-body"><canvas id="chartKategori" height="100"></canvas></div>
</div>

<div class="panel chart-panel">
  <div class="panel-head"><div><h3>Kumulatif Jam Downtime — Seluruh Mesin</h3><div class="sub">Akumulasi durasi perbaikan, menunjukkan pola beban maintenance</div></div></div>
  <div class="panel-body"><canvas id="chartKumulatif" height="90"></canvas></div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Komponen Paling Sering Diganti</h3><div class="sub" style="margin-left:18px;">Diambil dari kolom "Komponen yang Diganti" pada riwayat SIPPM</div></div>
  <div class="panel-body" style="padding:6px 18px 14px;">
  @foreach($komponenSering as $i => $k)
    <div class="rank-row">
      <div class="rank-num {{ $i==0?'top':'' }}">{{ $i+1 }}</div>
      <div class="rank-info"><div class="rn">{{ $k->komponen_diganti }}</div><div class="rs">{{ $k->kategori }}</div></div>
      <div class="rank-val">{{ $k->jumlah }}×</div>
    </div>
  @endforeach
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Ekspor Laporan</h3></div>
  <div class="panel-body">
    <div class="export-box">
      <div class="export-opt" role="button" tabindex="0" onclick="exportLaporanPdf('laporanContent', 'Laporan-SIMPM-Supervisor.pdf')"><div class="eo-ic">📄</div><div class="eo-title">Laporan PDF</div><div class="eo-sub">Ringkasan performa &amp; grafik siap cetak</div></div>
      <div class="export-opt" role="button" tabindex="0" onclick="exportLaporanExcel()"><div class="eo-ic">📊</div><div class="eo-title">Data Excel</div><div class="eo-sub">Riwayat maintenance mentah untuk analisis lanjutan</div></div>
    </div>
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
  const kategoriLabels = @json($trenKategori->pluck('label_periode'));
  renderStackedBarChart('chartKategori', kategoriLabels, [
    { label: 'Mekanik', data: @json($trenKategori->pluck('perbaikan_mekanik')), backgroundColor: '#E8952E' },
    { label: 'Elektrik', data: @json($trenKategori->pluck('perbaikan_elektrik')), backgroundColor: '#2B6CB0' },
    { label: 'Instrumentasi', data: @json($trenKategori->pluck('perbaikan_instrumentasi')), backgroundColor: '#6B4C9A' },
  ]);

  renderLineChart('chartKumulatif', kategoriLabels, [{
    label: 'Kumulatif downtime (jam)', data: @json($trenDowntimeKumulatif->pluck('downtime_jam')),
    borderColor: '#2B6CB0', backgroundColor: 'rgba(43,108,176,.18)', fill: true, tension: .3,
  }]);

  function exportLaporanExcel() {
    const tren = @json($trenKategori);
    const kumulatif = @json($trenDowntimeKumulatif);
    const komponen = @json($komponenSering);

    const rowsKategori = [['Periode', 'Perbaikan Mekanik', 'Perbaikan Elektrik', 'Perbaikan Instrumentasi']];
    tren.forEach(r => rowsKategori.push([r.label_periode, r.perbaikan_mekanik, r.perbaikan_elektrik, r.perbaikan_instrumentasi]));

    const rowsDowntime = [['Periode', 'Downtime Kumulatif (jam)']];
    kumulatif.forEach(r => rowsDowntime.push([r.label_periode, r.downtime_jam]));

    const rowsKomponen = [['Komponen Diganti', 'Kategori', 'Jumlah']];
    komponen.forEach(k => rowsKomponen.push([k.komponen_diganti, k.kategori, k.jumlah]));

    exportSheetsToExcel('Data-Laporan-SIMPM-Supervisor.xlsx', [
      { name: 'Perbaikan per Kategori', rows: rowsKategori },
      { name: 'Downtime Kumulatif', rows: rowsDowntime },
      { name: 'Komponen Sering Diganti', rows: rowsKomponen },
    ]);
  }
</script>
@endsection
