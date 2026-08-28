@extends('layouts.app')
@section('title', 'Detail Performa Mesin')

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>Detail Performa <span class="mono" style="font-weight:400;">{{ $mesin->nama }}</span></h3>
    <span class="badge {{ $mesin->statusBadgeClass() }}">{{ $mesin->status }}</span>
  </div>
  <div class="panel-body">
    <div class="kpi-gauge-row">
      <div class="kpi-gauge">
        <canvas id="gaugeOee" width="72" height="72"></canvas>
        <div class="gnum">{{ $mesin->oee }}%</div><div class="glabel">OEE</div>
      </div>
      <div class="kpi-gauge">
        <canvas id="gaugeAvail" width="72" height="72"></canvas>
        <div class="gnum">{{ $mesin->availability }}%</div><div class="glabel">Availability</div>
      </div>
      <div style="flex:1;min-width:180px;display:flex;flex-direction:column;gap:10px;">
        <div class="kv"><span class="k">MTTR</span><span class="v mono">{{ $mesin->mttr_jam }} jam</span></div>
        <div class="kv"><span class="k">MTBF</span><span class="v mono">{{ $mesin->mtbf_jam }} jam</span></div>
        <div class="kv"><span class="k">Total Downtime Bulan Ini</span><span class="v mono">{{ $mesin->downtime_bulan_ini_jam }} jam</span></div>
      </div>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Riwayat Perbaikan Terakhir</h3></div>
  <div class="panel-body" style="padding:0;">
    <table>
      <thead><tr><th>No. Laporan</th><th>Kategori</th><th>Komponen</th><th>Downtime</th><th>Teknisi</th><th>Selesai</th></tr></thead>
      <tbody>
      @forelse($riwayat as $r)
        <tr>
          <td class="mono">{{ $r->no_laporan }}</td>
          <td><span class="badge {{ $r->kategoriBadgeClass() }}">{{ $r->kategori }}</span></td>
          <td>{{ $r->komponen_diganti }}</td>
          <td class="mono">{{ $r->downtime_menit }} mnt</td>
          <td>{{ $r->teknisi_nama }}</td>
          <td class="mono">{{ $r->diselesaikan_pada->format('d M Y') }}</td>
        </tr>
      @empty
        <tr><td colspan="6" style="color:var(--ink-soft);">Belum ada riwayat.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
<a href="{{ route('supervisor.monitoring') }}" class="btn btn-outline">← Kembali</a>
@endsection

@section('scripts')
<script>
  function drawGauge(id, value, color) {
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
      type: 'doughnut',
      data: { datasets: [{ data: [value, 100 - value], backgroundColor: [color, '#EEF0F2'], borderWidth: 0 }] },
      options: { cutout: '75%', plugins: { legend: { display: false }, tooltip: { enabled: false } }, animation: { animateRotate: true } },
    });
  }
  drawGauge('gaugeOee', {{ $mesin->oee }}, {{ $mesin->oee >= 80 ? "'#1B7A43'" : ($mesin->oee >= 60 ? "'#E8952E'" : "'#C4361E'") }});
  drawGauge('gaugeAvail', {{ $mesin->availability }}, {{ $mesin->availability >= 80 ? "'#1B7A43'" : ($mesin->availability >= 60 ? "'#E8952E'" : "'#C4361E'") }});
</script>
@endsection
