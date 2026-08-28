@extends('layouts.app')
@section('title', 'Tambah Jadwal Maintenance Preventif')

@section('content')
<div class="panel">
  <div class="panel-body">
    <form method="POST" action="{{ route('supervisor.jadwal.simpan') }}">
      @csrf
      <div class="form-grid">
        <div class="field">
          <label>Mesin</label>
          <select name="mesin_id" required>
            @foreach($mesinList as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach
          </select>
        </div>
        <div class="field">
          <label>Jenis Pemeriksaan / PM</label>
          <input type="text" name="jenis_pm" placeholder="cth. Pelumasan bearing, Kalibrasi sensor" required>
        </div>
        <div class="field">
          <label>Teknisi Ditugaskan</label>
          <select name="teknisi_id" required>
            @foreach($teknisiList as $t)<option value="{{ $t->id }}">{{ $t->name }} ({{ $t->sub_role }})</option>@endforeach
          </select>
        </div>
        <div class="field">
          <label>Tanggal Mulai</label>
          <input type="date" name="tanggal" required>
        </div>
        <div class="field">
          <label>Interval Pengulangan</label>
          <select name="interval" required>
            <option>Harian</option><option selected>Mingguan</option><option>Bulanan</option><option>Tidak Berulang</option>
          </select>
        </div>
        <div class="field">
          <label>Estimasi Durasi</label>
          <input type="text" name="estimasi_durasi" placeholder="cth. 45 menit">
        </div>
        <div class="field span2">
          <label>Catatan / Checklist</label>
          <textarea name="catatan" placeholder="Rincian item yang perlu diperiksa (satu per baris)..."></textarea>
        </div>
      </div>
      <div class="action-bar">
        <button type="submit" class="btn btn-blue">Simpan Jadwal</button>
        <a href="{{ route('supervisor.maintenance') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
