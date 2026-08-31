<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'SIMPM') — SIMPM Project 2</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
</head>
<body>
@if (session('success') || session('error'))
<div id="flashData" data-success="{{ session('success') }}" data-error="{{ session('error') }}" hidden></div>
@endif

<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-mark">SIM<span>PM</span></div>
      <div class="brand-sub">PG Rendeng · Modul 2</div>
      <div class="brand-link"><span class="sq"></span>Tersinkron dari SIPPM</div>
    </div>

    <nav class="nav" aria-label="Navigasi utama">
      @auth
        @if(auth()->user()->isSupervisor())
          <div class="nav-group-label">Monitoring</div>
          <a href="{{ route('supervisor.dashboard') }}" class="nav-item {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}"><span class="ic">▤</span>Dashboard Performa</a>
          <a href="{{ route('supervisor.monitoring') }}" class="nav-item {{ request()->routeIs('supervisor.monitoring') || request()->routeIs('supervisor.detail-mesin') ? 'active' : '' }}"><span class="ic">▣</span>Performa Mesin</a>
          <div class="nav-group-label">Maintenance</div>
          <a href="{{ route('supervisor.maintenance') }}" class="nav-item {{ request()->routeIs('supervisor.maintenance') || request()->routeIs('supervisor.jadwal.*') ? 'active' : '' }}"><span class="ic">🛠</span>Jadwal Preventif</a>
          <a href="{{ route('supervisor.riwayat') }}" class="nav-item {{ request()->routeIs('supervisor.riwayat') ? 'active' : '' }}"><span class="ic">☰</span>Riwayat Maintenance</a>
          <div class="nav-group-label">Laporan</div>
          <a href="{{ route('supervisor.laporan') }}" class="nav-item {{ request()->routeIs('supervisor.laporan') ? 'active' : '' }}"><span class="ic">📈</span>Laporan &amp; Grafik</a>
          <div class="nav-group-label">Akun</div>
          <a href="{{ route('supervisor.profil') }}" class="nav-item {{ request()->routeIs('supervisor.profil') ? 'active' : '' }}"><span class="ic">⚙</span>Profil Saya</a>
        @elseif(auth()->user()->isTeknisi())
          <div class="nav-group-label">Menu</div>
          <a href="{{ route('teknisi.dashboard') }}" class="nav-item {{ request()->routeIs('teknisi.dashboard') || request()->routeIs('teknisi.jadwal.detail') ? 'active' : '' }}"><span class="ic">▤</span>Jadwal Maintenance Saya</a>
          <a href="{{ route('teknisi.riwayat') }}" class="nav-item {{ request()->routeIs('teknisi.riwayat') ? 'active' : '' }}"><span class="ic">☰</span>Riwayat Perbaikan Saya</a>
          <div class="nav-group-label">Performa</div>
          <a href="{{ route('teknisi.performa') }}" class="nav-item {{ request()->routeIs('teknisi.performa') ? 'active' : '' }}"><span class="ic">📈</span>Performa Mesin Saya</a>
          <div class="nav-group-label">Akun</div>
          <a href="{{ route('teknisi.profil') }}" class="nav-item {{ request()->routeIs('teknisi.profil') ? 'active' : '' }}"><span class="ic">⚙</span>Profil Saya</a>
        @elseif(auth()->user()->isManajer())
          <div class="nav-group-label">Eksekutif</div>
          <a href="{{ route('manajer.dashboard') }}" class="nav-item {{ request()->routeIs('manajer.dashboard') ? 'active' : '' }}"><span class="ic">▤</span>Dashboard Eksekutif</a>
          <a href="{{ route('manajer.performa') }}" class="nav-item {{ request()->routeIs('manajer.performa') ? 'active' : '' }}"><span class="ic">▣</span>Performa Seluruh Mesin</a>
          <a href="{{ route('manajer.maintenance') }}" class="nav-item {{ request()->routeIs('manajer.maintenance') ? 'active' : '' }}"><span class="ic">🛠</span>Analisis Maintenance</a>
          <div class="nav-group-label">Laporan</div>
          <a href="{{ route('manajer.laporan') }}" class="nav-item {{ request()->routeIs('manajer.laporan') ? 'active' : '' }}"><span class="ic">📈</span>Unduh Laporan</a>
          <div class="nav-group-label">Akun</div>
          <a href="{{ route('manajer.profil') }}" class="nav-item {{ request()->routeIs('manajer.profil') ? 'active' : '' }}"><span class="ic">⚙</span>Profil Saya</a>
        @endif
      @endauth
    </nav>

    <div class="sidebar-foot">Data performa &amp; grafik diperbarui otomatis dari riwayat maintenance SIPPM.</div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left">
        <button id="sidebarToggle" class="btn btn-outline btn-sm sidebar-toggle" type="button" aria-label="Buka atau tutup menu">☰</button>
        <div>
          <div class="topbar-crumb">{{ ucfirst(auth()->user()->role ?? '') }}</div>
          <div class="topbar-title">@yield('title')</div>
        </div>
      </div>
      <div class="topbar-user">
        <div class="topbar-user-info">
          <div class="topbar-user-name">{{ auth()->user()->name ?? '' }}</div>
          <div class="topbar-user-role">{{ auth()->user()->sub_role ?? '' }}</div>
        </div>
        <div class="avatar">{{ auth()->user()->initials() ?? 'U' }}</div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="logout-link">Keluar</button>
        </form>
      </div>
    </header>

    <main class="content">
      @if ($errors->any())
        <div class="callout warn" role="alert">{{ $errors->first() }}</div>
      @endif
      @yield('content')
    </main>
  </div>
</div>

@stack('lib-scripts')
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
