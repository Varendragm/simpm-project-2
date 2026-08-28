<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — SIMPM Project 2</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="splash" id="splashScreen">
  <div class="brand-mark">SIM<span style="color:#4C8FD6;">PM</span></div>
  <div class="bar"><div class="bar-fill"></div></div>
</div>

<div class="login-wrap">
  <div class="login-card">
    <h1>SIMPM</h1>
    <div class="sub">Sistem Informasi Monitoring Performa Mesin — PG Rendeng</div>

    @if ($errors->any())
      <div class="err-box">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
      @csrf

      <div class="login-role-row">
        <button type="button" class="lrt on" data-role="supervisor" onclick="selectLoginRole('supervisor', this)">Supervisor</button>
        <button type="button" class="lrt" data-role="teknisi" onclick="selectLoginRole('teknisi', this)">Teknisi</button>
        <button type="button" class="lrt" data-role="manajer" onclick="selectLoginRole('manajer', this)">Manajer</button>
      </div>

      <div class="field" style="margin-bottom:14px;">
        <label>Username</label>
        <input type="text" name="username" id="loginUsername" value="{{ old('username', 'sri.supervisor') }}" required>
      </div>
      <div class="field" style="margin-bottom:18px;">
        <label>Kata Sandi</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" class="btn btn-blue" style="width:100%;justify-content:center;">
        Masuk sebagai <span id="loginRoleLabel">&nbsp;Supervisor</span>
      </button>
    </form>

    <div class="login-note">Satu akun = satu peran. Akun Supervisor &amp; Teknisi sama seperti pada SIPPM — tidak perlu mendaftar ulang.</div>
  </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
<script>
  const DEMO_USERNAMES = { supervisor: 'sri.supervisor', teknisi: 'budi.teknisi', manajer: 'wahyu.manajer' };
  const ROLE_LABELS = { supervisor: 'Supervisor', teknisi: 'Teknisi', manajer: 'Manajer' };

  function selectLoginRole(role, btn) {
    document.querySelectorAll('.lrt').forEach(b => b.classList.toggle('on', b === btn));
    document.getElementById('loginUsername').value = DEMO_USERNAMES[role];
    document.getElementById('loginRoleLabel').textContent = ' ' + ROLE_LABELS[role];
  }
</script>
</body>
</html>
