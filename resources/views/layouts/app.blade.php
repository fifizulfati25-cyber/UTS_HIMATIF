<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Informasi publik Himpunan Mahasiswa Teknik Informatika.">
    <title>{{ $judulHalaman ?? 'HIMATIF — Himpunan Mahasiswa Teknik Informatika' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-himatif.png') }}">
    @include('layouts.styles')
</head>
<body>
<header class="site-header"><div class="site-bar">
    <a class="brand" href="{{ route('home') }}" aria-label="HIMATIF, beranda"><img class="brand-logo" src="{{ asset('images/logo-himatif.png') }}" alt="Logo HIMATIF Universitas Catur Insan Cendekia"><span>HIMATIF<small>Himpunan Mahasiswa Teknik Informatika</small></span></a>
    <nav class="public-nav" aria-label="Navigasi publik">
        <a href="{{ route('home') }}">Beranda</a><a href="{{ route('profil.index') }}">Profil</a><a href="{{ route('kegiatan.index') }}">Kegiatan</a><a href="{{ route('galeri.index') }}">Galeri</a><a href="{{ route('kontak.index') }}">Kontak</a>
        <a class="portal-link" href="{{ $himatifAccount ? route('portal.dashboard') : route('login') }}">{{ $himatifAccount ? 'Dashboard Anggota' : 'Portal Anggota' }}</a>
    </nav>
</div></header>
<main>
    @if (session('success'))<div class="alert alert-success" role="status" aria-live="polite">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif
    @yield('content')
</main>
<footer class="site-footer"><div class="footer-content"><img class="footer-logo" src="{{ asset('images/logo-himatif.png') }}" alt="Logo HIMATIF UCIC"><p><strong>HIMATIF</strong><br>Himpunan Mahasiswa Teknik Informatika · Informasi resmi dilengkapi oleh pengurus.</p></div></footer>
</body>
</html>