@extends('layouts.app')
@section('title', 'Dashboard Performa')

@section('content')
<div class="grid-stats">
  <div class="stat-card blue"><div class="stat-num" data-count="{{ $oeeRata }}">0<span class="unit">%</span></div><div class="stat-label">OEE Rata-rata</div></div>
  <div class="stat-card green"><div class="stat-num" data-count="{{ $availRata }}">0<span class="unit">%</span></div><div class="stat-label">Availability</div></div>
  <div class="stat-card amber"><div class="stat-num" data-count="{{ $mttrRata }}">0<span class="unit">jam</span></div><div class="stat-label">MTTR (Rata Waktu Perbaikan)</div></div>
  <div class="stat-card"><div class="stat-num" data-count="{{ $mtbfRata }}">0<span class="unit">jam</span></div><div class="stat-label">MTBF (Rata Antar Kerusakan)</div></div>
  <div class="stat-card red"><div class="stat-num" data-count="{{ $perluPerhatian }}">0</div><div class="stat-label">Mesin Perlu Perhatian</div></div>
</div>

<div class="panel chart-panel">
  <div class="panel-head">
    <div><h3>Tren Availability Mingguan — Seluruh Mesin</h3><div class="sub">6 minggu terakhir · sumber: agregat downtime dari riwayat maintenance SIPPM</div></div>
    <span class="chart-tag normal">Grafik Interaktif</span>
  </div>
  <div class="panel-body"><canvas id="chartAvailability" height="90"></canvas></div>
</div>

<div class="panel chart-panel">
  <div class="panel-head">
    <div><h3>Tren Downtime Bulanan per Mesin</h3><div class="sub">6 bulan terakhir · dihitung dari total durasi perbaikan</div></div>
    <span class="chart-tag gunung">Grafik Interaktif</span>
  </div>
  <div class="panel-body"><canvas id="chartDowntime" height="100"></canvas></div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Mesin Perlu Perhatian</h3><a href="{{ route('supervisor.monitoring') }}" class="btn btn-outline btn-sm">Lihat Semua Mesin</a></div>
  <div class="panel-body" style="padding:6px 18px;">
    @forelse($mesinPerluPerhatian as $i => $m)
      <div class="rank-row">
        <div class="rank-num {{ $i < 2 ? 'top' : '' }}">{{ $i + 1 }}</div>
        <div class="rank-info"><div class="rn">{{ $m->nama }}</div><div class="rs">{{ $m->jumlah_perbaikan_bulan_ini }} kali perbaikan bulan ini</div></div>
        <div class="rank-val" style="color:var(--red);">{{ $m->downtime_bulan_ini_jam }} jam</div>
      </div>
    @empty
      <div style="padding:14px 0;color:var(--ink-soft);">Semua mesin dalam kondisi normal.</div>
    @endforelse
  </div>
</div>
@endsection

@section('scripts')
<script>
  const availLabels = @json($trenAvailability->pluck('label_periode'));
  const availData = @json($trenAvailability->pluck('availability'));
  renderLineChart('chartAvailability', availLabels, [{
    label: 'Availability rata-rata (%)', data: availData,
    borderColor: '#2B6CB0', backgroundColor: 'rgba(43,108,176,.12)', fill: true, tension: .35,
  }]);

  const downtimeByMesin = @json($trenDowntimePerMesin->map(fn($rows) => $rows->pluck('downtime_jam')));
  const downtimeLabels = @json(optional($trenDowntimePerMesin->first())?->pluck('label_periode') ?? []);
  const colors = ['#2B6CB0', '#E8952E', '#1B7A43', '#6B4C9A'];
  let ci = 0;
  const downtimeDatasets = Object.entries(downtimeByMesin).map(([nama, data]) => ({
    label: nama, data: data, borderColor: colors[ci % colors.length],
    backgroundColor: colors[ci++ % colors.length] + '55', fill: true, tension: .3,
  }));
  renderLineChart('chartDowntime', downtimeLabels, downtimeDatasets);
</script>
@endsection
