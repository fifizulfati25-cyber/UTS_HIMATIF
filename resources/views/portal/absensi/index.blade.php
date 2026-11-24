@extends('layouts.portal')

@section('content')
    <p class="eyebrow">Area Anggota</p><h1 class="page-heading">{{ $judulHalaman }}</h1><p class="intro">Riwayat ini hanya menampilkan kehadiran yang terhubung dengan akunmu.</p>
    <div class="toolbar"><span>Identitas: <strong>{{ $himatifAccount['nama'] }}</strong> · {{ $himatifAccount['nim'] }}</span><a class="button button-primary" href="{{ route('portal.absensi.create') }}">+ Isi Absensi</a></div>
    @if (count($daftarAbsensi) === 0)<p class="empty">Belum ada catatan kehadiran untuk akun ini.</p>@else<div class="table-wrap"><table><thead><tr><th>Rapat</th><th>Tanggal rapat</th><th>Waktu hadir</th><th>Detail</th></tr></thead><tbody>@foreach ($daftarAbsensi as $absensi)<tr><td>{{ $absensi['rapat'] }}</td><td>{{ \Carbon\Carbon::parse($absensi['tanggal_rapat'])->locale('id')->translatedFormat('d F Y') }}</td><td>{{ $absensi['waktu_hadir'] }}</td><td><a class="button button-small" href="{{ route('portal.absensi.show', $absensi['id']) }}">Lihat</a></td></tr>@endforeach</tbody></table></div>@endif
@endsection