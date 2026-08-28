@extends('layouts.app')
@section('title', 'Dashboard Eksekutif')

@section('content')
<div class="callout">Tampilan Manajer bersifat ringkas dan hanya-baca (read-only) — untuk pengambilan keputusan strategis, tanpa input operasional harian.</div>
<div class="grid-stats">
  <div class="stat-card blue"><div class="stat-num" data-count="{{ $stat['oee_pabrik'] }}">0<span class="unit">%</span></div><div class="stat-label">OEE Pabrik (Rata-rata)</div></div>
  <div class="stat-card green"><div class="stat-num" data-count="{{ $stat['availability_pabrik'] }}">0<span class="unit">%</span></div><div class="stat-label">Availability Pabrik</div></div>
  <div class="stat-card red"><div class="stat-num" data-count="{{ $stat['downtime_bulan_ini'] }}">0<span class="unit">jam</span></div><div class="stat-label">Total Downtime Bulan Ini</div></div>
  <div class="stat-card amber"><div class="stat-num" data-count="{{ $stat['total_perbaikan'] }}">0</div><div class="stat-label">Total Perbaikan Bulan Ini</div></div>
  <div class="stat-card purple"><div class="stat-num" style="font-size:19px;">{{ $stat['mesin_bermasalah']->nama }}</div><div class="stat-label">Mesin Paling Bermasalah</div></div>
</div>

<div class="panel chart-panel">
  <div class="panel-head"><div><h3>Tren Downtime Bulanan per Mesin</h3><div class="sub">Perbandingan beban perbaikan 4 mesin gilingan, 6 bulan terakhir</div></div></div>
  <div class="panel-body"><canvas id="chartDowntimeManajer" height="100"></canvas></div>
</div>
@endsection

@section('scripts')
<script>
  const grouped = @json($trenDowntimePerMesin->groupBy('mesin.nama')->map(fn($r) => $r->pluck('downtime_jam')));
  const labels = @json(optional($trenDowntimePerMesin->groupBy('mesin.nama')->first())?->pluck('label_periode') ?? []);
  const colors = ['#2B6CB0', '#E8952E', '#1B7A43', '#6B4C9A'];
  let i = 0;
  const datasets = Object.entries(grouped).map(([nama, data]) => ({
    label: nama, data: data, borderColor: colors[i % colors.length],
    backgroundColor: colors[i++ % colors.length] + '40', fill: true, tension: .3,
  }));
  renderLineChart('chartDowntimeManajer', labels, datasets);
</script>
@endsection
