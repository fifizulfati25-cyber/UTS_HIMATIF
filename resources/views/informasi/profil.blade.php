@extends('layouts.app')

@section('content')
    <section class="profile-hero">
        <p class="eyebrow">Himpunan Mahasiswa Teknik Informatika UCIC</p>
        <h1 class="page-heading">{{ $judulHalaman }}</h1>
        <p>Wadah pengembangan potensi, kepedulian, dan adaptasi mahasiswa Teknik Informatika.</p>
        <span class="profile-period">Periode {{ $periode }}</span>
    </section>

    <section class="section profile-section">
        <div class="profile-section-heading"><p class="eyebrow">Tentang Organisasi</p><h2>Tentang HIMATIF</h2></div>
        <article class="card profile-about">
            <p>HIMATIF adalah Himpunan Mahasiswa Teknik Informatika di Universitas Catur Insan Cendekia (UCIC). Organisasi ini menjadi wadah mahasiswa untuk mengembangkan potensi, membangun kepedulian, dan menjadi pribadi yang proaktif serta adaptif.</p>
        </article>
    </section>

    <section class="section profile-section">
        <div class="profile-section-heading"><p class="eyebrow">Arah Organisasi</p><h2>Visi dan Misi</h2></div>
        <article class="profile-vision">
            <p class="eyebrow">Visi Kami</p>
            <h3>Mahasiswa yang proaktif dan adaptif</h3>
            <p>{{ $visi }}</p>
        </article>
        <div class="profile-section-heading profile-subheading"><p class="eyebrow">Langkah Kami</p><h2>Misi Kami</h2></div>
        <div class="profile-card-grid">
            @foreach ($misi as $isiMisi)
                <article class="card profile-info-card"><span class="profile-number">{{ $loop->iteration }}</span><p>{{ $isiMisi }}</p></article>
            @endforeach
        </div>
    </section>

    <section class="section profile-section" id="struktur-organisasi">
        <div class="profile-section-heading"><p class="eyebrow">Periode {{ $periode }}</p><h2>Struktur Organisasi HIMATIF UCIC Periode {{ $periode }}</h2></div>
        <h3 class="organization-subtitle">Badan Pengurus Harian (BPH)</h3>
        <div class="profile-card-grid">
            @foreach ($bph as $pengurus)
                <article class="card profile-person-card"><p class="eyebrow">{{ $pengurus['jabatan'] }}</p><h3>{{ $pengurus['nama'] }}</h3></article>
            @endforeach
        </div>
        <h3 class="organization-subtitle division-heading">Bidang / Divisi</h3>
        <div class="profile-card-grid">
            @foreach ($bidang as $namaBidang)
                <article class="card profile-division-card"><span class="division-mark" aria-hidden="true"></span><h3>{{ $namaBidang }}</h3></article>
            @endforeach
        </div>
    </section>
@endsection