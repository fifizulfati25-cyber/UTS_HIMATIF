@extends('layouts.portal')

@section('content')
    <p class="eyebrow">Area Anggota</p><h1 class="page-heading">{{ $judulHalaman }}</h1>
    @if ($absensi)<article class="card"><p><strong>Nama:</strong> {{ $absensi['nama'] }}</p><p><strong>NIM:</strong> {{ $absensi['nim'] }}</p><p><strong>Rapat:</strong> {{ $absensi['rapat'] }}</p><p><strong>Tanggal rapat:</strong> {{ \Carbon\Carbon::parse($absensi['tanggal_rapat'])->locale('id')->translatedFormat('d F Y') }}</p><p><strong>Waktu hadir:</strong> {{ $absensi['waktu_hadir'] }}</p></article><a class="button" href="{{ route('portal.absensi.index') }}">Kembali ke riwayat</a>@else<p class="empty">Catatan kehadiran tidak ditemukan pada akun ini.</p><a class="button" href="{{ route('portal.absensi.index') }}">Kembali</a>@endif
@endsection