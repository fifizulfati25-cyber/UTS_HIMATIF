@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="hero-content"><p class="eyebrow">Organisasi Mahasiswa Teknik Informatika</p><h1>Himpunan Mahasiswa Teknik Informatika</h1><p>Wadah pengembangan, kolaborasi, dan kegiatan mahasiswa Teknik Informatika untuk belajar dan bertumbuh bersama.</p><div class="hero-actions"><a class="button button-light" href="{{ route('profil.index') }}">Tentang HIMATIF</a><a class="button button-outline" href="{{ route('kegiatan.index') }}">Jelajahi kegiatan</a></div></div>
        <aside class="hero-note"><img class="hero-logo" src="{{ asset('images/logo-himatif.png') }}" alt="Logo resmi HIMATIF Universitas Catur Insan Cendekia"><strong>Ruang untuk berkembang bersama</strong><p>Kenali profil organisasi, program kerja, dokumentasi, dan informasi kontak HIMATIF.</p></aside>
    </section>
    <section class="section split">
        <article class="card"><p class="eyebrow">Tentang</p><h2>Organisasi mahasiswa yang terbuka untuk berkolaborasi</h2><p>HIMATIF merupakan Himpunan Mahasiswa Teknik Informatika. Informasi sejarah dan profil resmi dapat dilengkapi setelah dikonfirmasi oleh pengurus.</p><a href="{{ route('profil.index') }}">Baca profil HIMATIF →</a></article>
        <article class="card home-vision-card"><p class="eyebrow">Visi & Misi</p><h2>Proaktif, adaptif, dan peduli</h2><p><strong>Visi:</strong> {{ $visi }}</p><strong>Misi:</strong><ol class="home-mission-list">@foreach ($misi as $isiMisi)<li>{{ $isiMisi }}</li>@endforeach</ol><a href="{{ route('profil.index') }}">Kenali arah HIMATIF →</a></article>
    </section>
    <section class="section">
        <div class="section-heading"><div><p class="eyebrow">Program Kerja</p><h2>Kegiatan HIMATIF</h2></div><a href="{{ route('kegiatan.index') }}">Lihat semua kegiatan →</a></div>
        @if (count($programUnggulan) === 0)<p class="empty">Belum ada program kerja yang ditampilkan.</p>@else
            <div class="grid">@foreach ($programUnggulan as $kegiatan)<article class="card feature-card"><p class="eyebrow">{{ $kegiatan['kategori'] }}</p><h2>{{ $kegiatan['nama'] }}</h2><p class="meta">{{ \Carbon\Carbon::parse($kegiatan['tanggal'])->locale('id')->translatedFormat('d F Y') }} · {{ $kegiatan['lokasi'] }}</p><p>{{ \Illuminate\Support\Str::limit($kegiatan['deskripsi'], 115) }}</p><a class="button button-small" href="{{ route('kegiatan.show', $kegiatan['id']) }}">Lihat detail</a></article>@endforeach</div>
        @endif
    </section>
    <section class="section split">
        <article class="card"><p class="eyebrow">Dokumentasi</p><h2>Momen kegiatan HIMATIF</h2><p>Galeri menampilkan dokumentasi yang tersedia. Foto belum ditambahkan ke project ini.</p><a href="{{ route('galeri.index') }}">Buka galeri →</a></article>
        <article class="card"><p class="eyebrow">Kontak</p><h2>Hubungi HIMATIF</h2><p>Informasi kontak resmi belum tersedia. Silakan gunakan halaman kontak setelah pengurus melengkapi datanya.</p><a href="{{ route('kontak.index') }}">Lihat kontak →</a></article>
    </section>
@endsection