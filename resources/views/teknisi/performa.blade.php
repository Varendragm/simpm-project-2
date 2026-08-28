@extends('layouts.app')
@section('title', 'Performa Mesin Saya')

@section('content')
<div class="grid-stats">
  <div class="stat-card blue"><div class="stat-num" data-count="{{ $stat['rata_waktu'] }}">0<span class="unit">jam</span></div><div class="stat-label">Rata-rata Waktu Perbaikan Saya</div></div>
  <div class="stat-card green"><div class="stat-num" data-count="{{ $stat['selesai_3bulan'] }}">0</div><div class="stat-label">Perbaikan Diselesaikan (3 Bulan)</div></div>
  <div class="stat-card amber"><div class="stat-num" data-count="{{ $stat['mesin_sering'] }}">0</div><div class="stat-label">Mesin Sering Ditangani</div></div>
</div>
<div class="panel chart-panel">
  <div class="panel-head"><div><h3>Waktu Penyelesaian Perbaikan per Minggu</h3><div class="sub">Dihitung dari selisih waktu mulai &amp; selesai pada form hasil penanganan</div></div></div>
  <div class="panel-body"><canvas id="chartWaktuSaya" height="90"></canvas></div>
</div>
@endsection

@section('scripts')
<script>
  renderLineChart('chartWaktuSaya',
    ['Mgg 1','Mgg 2','Mgg 3','Mgg 4','Mgg 5','Mgg 6'],
    [{ label: 'Waktu rata-rata (jam)', data: [2.2,1.9,2.5,1.5,1.7,1.3], borderColor:'#1B7A43', backgroundColor:'rgba(27,122,67,.15)', fill:true, tension:.35 }]
  );
</script>
@endsection
