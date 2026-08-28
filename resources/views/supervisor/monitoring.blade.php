@extends('layouts.app')
@section('title', 'Performa Mesin')

@section('content')
<div class="filter-row">
  <input type="text" class="fsearch" data-table-search="tblMesin" placeholder="Cari mesin...">
  <form method="GET">
    <select class="fsel" name="status" onchange="this.form.submit()">
      <option {{ !request('status') ? 'selected' : '' }}>Semua Status</option>
      <option {{ request('status')=='Normal' ? 'selected' : '' }}>Normal</option>
      <option {{ request('status')=='Perlu Perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
      <option {{ request('status')=='Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
    </select>
  </form>
</div>
<div class="panel">
  <div class="panel-head"><h3>Performa Seluruh Mesin</h3></div>
  <div class="panel-body" style="padding:0;">
    <table id="tblMesin" data-sortable>
      <thead><tr><th>Mesin</th><th>Status</th><th>Availability</th><th>OEE</th><th>Downtime Bulan Ini</th><th>Jumlah Perbaikan</th><th></th></tr></thead>
      <tbody>
      @foreach($mesinList as $m)
        <tr>
          <td><strong>{{ $m->nama }}</strong></td>
          <td><span class="badge {{ $m->statusBadgeClass() }}">{{ $m->status }}</span></td>
          <td>
            <div class="progress-track" style="width:90px;display:inline-block;vertical-align:middle;">
              <div class="progress-fill {{ $m->status=='Normal'?'green':($m->status=='Dalam Perbaikan'?'amber':'red') }}" data-width="{{ $m->availability }}"></div>
            </div>
            <span class="mono" style="font-size:11.5px;">{{ $m->availability }}%</span>
          </td>
          <td class="mono">{{ $m->oee }}%</td>
          <td class="mono">{{ $m->downtime_bulan_ini_jam }} jam</td>
          <td class="mono">{{ $m->jumlah_perbaikan_bulan_ini }}</td>
          <td><a href="{{ route('supervisor.detail-mesin', $m) }}" class="btn btn-outline btn-sm">Detail</a></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
