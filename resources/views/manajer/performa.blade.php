@extends('layouts.app')
@section('title', 'Performa Seluruh Mesin')

@section('content')
<div class="machine-tab-row">
  <button class="mtab {{ !$mesinAktif ? 'on' : '' }}">Semua Mesin</button>
  @foreach($mesinList as $m)
    <button class="mtab {{ $mesinAktif && $mesinAktif->id==$m->id ? 'on' : '' }}" data-mesin-id="{{ $m->id }}">{{ $m->nama }}</button>
  @endforeach
</div>

<div class="panel chart-panel">
  <div class="panel-head"><div><h3>Perbandingan OEE Antar Mesin</h3><div class="sub">Rata-rata Agustus 2026</div></div></div>
  <div class="panel-body"><canvas id="chartOeeCompare" height="90"></canvas></div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Ringkasan Performa per Mesin</h3></div>
  <div class="panel-body" style="padding:0;">
    <table data-sortable>
      <thead><tr><th>Mesin</th><th>OEE</th><th>Availability</th><th>MTTR</th><th>MTBF</th><th>Downtime Bulan Ini</th></tr></thead>
      <tbody>
      @foreach($mesinList as $m)
        <tr>
          <td><strong>{{ $m->nama }}</strong></td>
          <td class="mono">{{ $m->oee }}%</td>
          <td class="mono">{{ $m->availability }}%</td>
          <td class="mono">{{ $m->mttr_jam }} jam</td>
          <td class="mono">{{ $m->mtbf_jam }} jam</td>
          <td class="mono">{{ $m->downtime_bulan_ini_jam }} jam</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('scripts')
<script>
  renderBarChart('chartOeeCompare',
    @json($mesinList->pluck('nama')),
    [{ label: 'OEE (%)', data: @json($mesinList->pluck('oee')),
       backgroundColor: @json($mesinList->map(fn($m) => $m->oee >= 80 ? '#1B7A43' : ($m->oee >= 60 ? '#E8952E' : '#C4361E'))) }]
  );
</script>
@endsection
