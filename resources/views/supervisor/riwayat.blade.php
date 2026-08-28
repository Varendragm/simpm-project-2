@extends('layouts.app')
@section('title', 'Riwayat Maintenance')

@section('content')
<div class="callout sync">🔄 Data pada tabel ini otomatis tersinkron dari <strong>SIPPM</strong> (Sistem Pelaporan Kerusakan Mesin) setiap kali Teknisi mengirim hasil penanganan. Tidak dapat diedit dari SIMPM.</div>
<div class="filter-row">
  <input type="text" class="fsearch" data-table-search="tblRiwayat" placeholder="Cari laporan...">
  <form method="GET" style="display:flex;gap:8px;">
    <select class="fsel" name="mesin_id" onchange="this.form.submit()">
      <option value="">Semua Mesin</option>
      @foreach($mesinList as $m)<option value="{{ $m->id }}" {{ request('mesin_id')==$m->id?'selected':'' }}>{{ $m->nama }}</option>@endforeach
    </select>
    <select class="fsel" name="kategori" onchange="this.form.submit()">
      <option>Semua Kategori</option><option>Mekanik</option><option>Elektrik</option><option>Instrumentasi</option>
    </select>
  </form>
</div>
<div class="panel">
  <div class="panel-head"><h3>Riwayat Maintenance Seluruh Mesin</h3></div>
  <div class="panel-body" style="padding:0;">
    <table id="tblRiwayat" data-sortable>
      <thead><tr><th>No. Laporan</th><th>Mesin</th><th>Kategori</th><th>Komponen Diganti</th><th>Downtime</th><th>Teknisi</th><th>Diselesaikan</th></tr></thead>
      <tbody>
      @foreach($riwayat as $r)
        <tr>
          <td class="mono">{{ $r->no_laporan }}</td>
          <td>{{ $r->mesin->nama }}</td>
          <td><span class="badge {{ $r->kategoriBadgeClass() }}">{{ $r->kategori }}</span></td>
          <td>{{ $r->komponen_diganti }}</td>
          <td class="mono">{{ $r->downtime_menit }} mnt</td>
          <td>{{ $r->teknisi_nama }}</td>
          <td class="mono">{{ $r->diselesaikan_pada->format('d M Y') }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
