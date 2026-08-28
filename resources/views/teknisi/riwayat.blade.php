@extends('layouts.app')
@section('title', 'Riwayat Perbaikan Saya')

@section('content')
<div class="callout sync">🔄 Riwayat ini sama dengan riwayat pekerjaan Anda di SIPPM — ditampilkan ulang di sini agar dapat dibandingkan dengan performa mesin.</div>
<div class="filter-row"><input type="text" class="fsearch" data-table-search="tblRiwayatSaya" placeholder="Cari..."></div>
<div class="panel">
  <div class="panel-head"><h3>Riwayat Perbaikan Saya</h3></div>
  <div class="panel-body" style="padding:0;">
    <table id="tblRiwayatSaya" data-sortable>
      <thead><tr><th>No. Laporan</th><th>Mesin</th><th>Tindakan</th><th>Downtime</th><th>Diselesaikan</th></tr></thead>
      <tbody>
      @forelse($riwayat as $r)
        <tr>
          <td class="mono">{{ $r->no_laporan }}</td>
          <td>{{ $r->mesin->nama }}</td>
          <td>{{ $r->tindakan }}</td>
          <td class="mono">{{ $r->downtime_menit }} menit</td>
          <td class="mono">{{ $r->diselesaikan_pada->format('d M Y') }}</td>
        </tr>
      @empty
        <tr><td colspan="5" style="color:var(--ink-soft);">Belum ada riwayat.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
