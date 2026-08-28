@extends('layouts.app')
@section('title', 'Jadwal Maintenance Preventif')

@section('content')
<div style="margin-bottom:14px;">
  <a href="{{ route('supervisor.jadwal.tambah') }}" class="btn btn-blue">+ Tambah Jadwal PM</a>
  <input type="text" class="fsearch" data-table-search="tblJadwal" placeholder="Cari jadwal..." style="margin-left:8px;">
</div>
<div class="panel">
  <div class="panel-head"><h3>Jadwal Maintenance Preventif</h3></div>
  <div class="panel-body" style="padding:0;">
    <table id="tblJadwal" data-sortable>
      <thead><tr><th>Mesin</th><th>Jenis PM</th><th>Teknisi</th><th>Jadwal</th><th>Interval</th><th>Status</th></tr></thead>
      <tbody>
      @foreach($jadwal as $j)
        <tr>
          <td><strong>{{ $j->mesin->nama }}</strong></td>
          <td>{{ $j->jenis_pm }}</td>
          <td>{{ $j->teknisi->name }}</td>
          <td class="mono">{{ $j->tanggal->format('d M Y') }}</td>
          <td>{{ $j->interval }}</td>
          <td><span class="badge {{ $j->statusBadgeClass() }}">{{ $j->status }}</span></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
