@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Profil Saya</h3></div>
  <div class="panel-body">
    <form method="POST" action="{{ route('manajer.profil.update') }}">
      @csrf @method('PUT')
      <div class="form-grid">
        <div class="field"><label>Nama Lengkap</label><input type="text" name="name" value="{{ auth()->user()->name }}"></div>
        <div class="field"><label>Username</label><input type="text" value="{{ auth()->user()->username }}" disabled style="background:#F5F6F7;"></div>
        <div class="field"><label>No. HP</label><input type="text" name="no_hp" value="{{ auth()->user()->no_hp }}"></div>
        <div class="field"><label>Jabatan</label><input type="text" value="{{ auth()->user()->sub_role }}" disabled style="background:#F5F6F7;"></div>
      </div>
      <div class="action-bar">
        <button type="submit" class="btn btn-blue">Simpan Perubahan</button>
        <a href="{{ route('manajer.dashboard') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
