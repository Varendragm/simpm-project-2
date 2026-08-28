@extends('layouts.app')
@section('title', 'Jadwal Maintenance Saya')

@section('content')
<div class="grid-stats">
  <div class="stat-card blue"><div class="stat-num" data-count="{{ $stat['minggu_ini'] }}">0</div><div class="stat-label">Jadwal PM Minggu Ini</div></div>
  <div class="stat-card amber"><div class="stat-num" data-count="{{ $stat['jatuh_tempo'] }}">0</div><div class="stat-label">Jatuh Tempo Hari Ini</div></div>
  <div class="stat-card green"><div class="stat-num" data-count="{{ $stat['selesai_bulan_ini'] }}">0</div><div class="stat-label">PM Selesai Bulan Ini</div></div>
</div>
<div class="panel">
  <div class="panel-head"><h3>Jadwal Maintenance Preventif Saya</h3></div>
  <div class="panel-body" style="padding:0;">
    <table>
      <thead><tr><th>Mesin</th><th>Jenis PM</th><th>Jadwal</th><th>Status</th><th></th></tr></thead>
      <tbody>
      @forelse($jadwal as $j)
        <tr>
          <td><strong>{{ $j->mesin->nama }}</strong></td>
          <td>{{ $j->jenis_pm }}</td>
          <td class="mono">{{ $j->tanggal->format('d M Y') }}</td>
          <td><span class="badge {{ $j->statusBadgeClass() }}">{{ $j->status }}</span></td>
          <td><a href="{{ route('teknisi.jadwal.detail', $j) }}" class="btn {{ $j->status=='Jatuh Tempo Hari Ini'?'btn-primary':'btn-outline' }} btn-sm">{{ $j->status=='Jatuh Tempo Hari Ini'?'Buka':'Lihat' }}</a></td>
        </tr>
      @empty
        <tr><td colspan="5" style="color:var(--ink-soft);">Tidak ada jadwal aktif.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
