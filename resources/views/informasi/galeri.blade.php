@extends('layouts.app')

@section('content')
    <p class="eyebrow">Dokumentasi</p><h1 class="page-heading">{{ $judulHalaman }}</h1><p class="intro">Foto resmi kegiatan belum tersedia di project ini.</p>
    @if (count($daftarGaleri) === 0)<p class="empty">Belum ada kegiatan untuk ditampilkan.</p>@else<div class="grid">@foreach ($daftarGaleri as $item)<article class="card"><div class="placeholder">Foto dokumentasi belum tersedia</div><h2 style="margin-top:16px">{{ $item['nama'] }}</h2><p class="meta">{{ \Carbon\Carbon::parse($item['tanggal'])->locale('id')->translatedFormat('d F Y') }}</p><p>{{ $item['foto']->getKeterangan() }}</p></article>@endforeach</div>@endif
@endsection