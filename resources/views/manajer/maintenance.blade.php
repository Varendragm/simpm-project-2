@extends('layouts.app')
@section('title', 'Analisis Maintenance')

@section('content')
<div class="panel chart-panel">
  <div class="panel-head"><div><h3>Jumlah Perbaikan per Kategori per Bulan</h3><div class="sub">Seluruh pabrik</div></div></div>
  <div class="panel-body"><canvas id="chartKategoriManajer" height="100"></canvas></div>
</div>
<div class="panel">
  <div class="panel-head"><h3>Ranking Mesin Bermasalah</h3></div>
  <div class="panel-body" style="padding:6px 18px;">
  @foreach($ranking as $i => $m)
    <div class="rank-row">
      <div class="rank-num {{ $i<2?'top':'' }}">{{ $i+1 }}</div>
      <div class="rank-info"><div class="rn">{{ $m->nama }}</div><div class="rs">OEE {{ $m->oee }}% · {{ $m->jumlah_perbaikan_bulan_ini }} perbaikan bulan ini</div></div>
      <div class="rank-val" style="{{ $i<2?'color:var(--red);':'' }}">{{ $m->downtime_bulan_ini_jam }} jam</div>
    </div>
  @endforeach
  </div>
</div>
@endsection

@section('scripts')
<script>
  const labels = @json($trenKategori->pluck('label_periode'));
  renderStackedBarChart('chartKategoriManajer', labels, [
    { label: 'Mekanik', data: @json($trenKategori->pluck('perbaikan_mekanik')), backgroundColor: '#E8952E' },
    { label: 'Elektrik', data: @json($trenKategori->pluck('perbaikan_elektrik')), backgroundColor: '#2B6CB0' },
    { label: 'Instrumentasi', data: @json($trenKategori->pluck('perbaikan_instrumentasi')), backgroundColor: '#6B4C9A' },
  ]);
</script>
@endsection
