@extends('layouts.app')
@section('title', 'Detail Jadwal PM')

@section('content')
@php($progres = $schedule->progresChecklist())
<div class="panel">
  <div class="panel-head">
    <h3>Detail Jadwal PM — {{ $schedule->mesin->nama }}</h3>
    <span class="badge {{ $schedule->statusBadgeClass() }}">{{ $schedule->status }}</span>
  </div>
  <div class="panel-body">
    <div class="detail-grid">
      <div>
        <div class="kv"><span class="k">Jenis PM</span><span class="v">{{ $schedule->jenis_pm }}</span></div>
        <div class="kv"><span class="k">Interval</span><span class="v">{{ $schedule->interval }}</span></div>
      </div>
      <div>
        <div class="kv"><span class="k">Jadwal</span><span class="v mono">{{ $schedule->tanggal->format('d M Y') }}</span></div>
        <div class="kv"><span class="k">Estimasi Durasi</span><span class="v mono">{{ $schedule->estimasi_durasi ?? '—' }}</span></div>
      </div>
    </div>

    @if($schedule->checklist->count())
    <div style="margin-top:16px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
        <div class="k" style="font-size:12px;color:var(--ink-soft);">Checklist Pemeriksaan</div>
        <div class="k" style="font-size:11.5px;" data-checklist-progress-label>{{ $progres['selesai'] }} / {{ $progres['total'] }} item selesai</div>
      </div>
      <div class="progress-track" style="margin-bottom:12px;">
        <div class="progress-fill green" data-checklist-progress style="width:{{ $progres['persen'] }}%;"></div>
      </div>
      <ul class="checklist">
        @foreach($schedule->checklist as $item)
          <li class="{{ $item->is_done ? 'done' : '' }}" data-item-id="{{ $item->id }}">
            <span class="chk"></span>{{ $item->item }}
          </li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="action-bar">
      @if($schedule->status !== 'Selesai')
      <form method="POST" action="{{ route('teknisi.jadwal.selesai', $schedule) }}">
        @csrf
        <button type="submit" class="btn btn-amber">Tandai Selesai &amp; Catat Hasil</button>
      </form>
      @endif
      <a href="{{ route('teknisi.dashboard') }}" class="btn btn-outline">Kembali</a>
    </div>
  </div>
</div>
@endsection
