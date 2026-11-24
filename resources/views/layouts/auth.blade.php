<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $judulHalaman ?? 'Portal HIMATIF' }}</title><link rel="icon" type="image/png" href="{{ asset('favicon-himatif.png') }}">@include('layouts.styles')</head>
<body><main class="login-page">
    <section class="login-aside"><a class="brand" href="{{ route('home') }}" style="color:white"><img class="brand-logo" src="{{ asset('images/logo-himatif.png') }}" alt="Logo HIMATIF Universitas Catur Insan Cendekia"><span>HIMATIF<small style="color:#dce9f6">Himpunan Mahasiswa Teknik Informatika</small></span></a><h1>Ruang kolaborasi anggota HIMATIF.</h1><p>Masuk ke portal internal untuk melihat jadwal rapat, mengisi absensi, dan memeriksa riwayat kehadiran.</p></section>
    <section class="login-card-wrap"><div class="login-card">
        <a class="brand" href="{{ route('home') }}"><img class="brand-logo" src="{{ asset('images/logo-himatif.png') }}" alt="Logo HIMATIF Universitas Catur Insan Cendekia"><span>HIMATIF<small>Portal anggota</small></span></a>
        @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif
        @yield('content')
    </div></section>
</main></body></html>