<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $judulHalaman ?? 'Portal Anggota HIMATIF' }}</title><link rel="icon" type="image/png" href="{{ asset('favicon-himatif.png') }}">@include('layouts.styles')</head>
<body class="portal-shell">
<header class="portal-top"><div class="site-bar">
    <a class="brand" href="{{ route('portal.dashboard') }}"><img class="brand-logo" src="{{ asset('images/logo-himatif.png') }}" alt="Logo HIMATIF Universitas Catur Insan Cendekia"><span>Portal HIMATIF<small>{{ $himatifAccount['nama'] ?? 'Area Anggota' }}</small></span></a>
    <nav class="portal-nav" aria-label="Navigasi portal anggota">
        <a href="{{ route('portal.dashboard') }}">Dashboard</a><a href="{{ route('portal.rapat.index') }}">Jadwal Rapat</a><a href="{{ route('portal.absensi.create') }}">Absensi</a><a href="{{ route('portal.absensi.index') }}">Riwayat Kehadiran</a>
        @if (($himatifAccount['role'] ?? null) === 'pengurus')<a href="{{ route('pengurus.anggota.index') }}">Verifikasi Anggota</a><a href="{{ route('pengurus.absensi.index') }}">Rekap Pengurus</a><a href="{{ route('kegiatan.index') }}">Kelola Kegiatan</a>@endif
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="button logout-button button-small" type="submit">Logout</button></form>
    </nav>
</div></header>
<main class="portal-main">
    @if (session('success'))<div class="alert alert-success" role="status" aria-live="polite">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif
    @yield('content')
</main>
<footer><div class="footer-content"><img class="footer-logo" src="{{ asset('images/logo-himatif.png') }}" alt="Logo HIMATIF UCIC"><p><strong>HIMATIF</strong><br>Portal internal · Data hanya untuk anggota terverifikasi.</p></div></footer>
</body>
</html>