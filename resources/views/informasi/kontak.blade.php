@extends('layouts.app')

@section('content')
    <p class="eyebrow">Hubungi Kami</p><h1 class="page-heading">{{ $judulHalaman }}</h1><p class="intro">Kontak resmi belum tersedia pada berkas project.</p>
    <section class="card"><h2>{{ $kontak->getNama() }}</h2><p><strong>Bagian:</strong> {{ $kontak->getJabatan() }}</p><p><strong>Kontak:</strong> {{ $kontak->getKontak() }}</p><p class="muted">Pengurus dapat melengkapi bagian ini menggunakan informasi kontak organisasi yang sudah dikonfirmasi.</p></section>
@endsection