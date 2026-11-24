@extends('layouts.app')

@section('content')
    <p class="eyebrow">Program Kerja HIMATIF</p>
    <h1 class="page-heading">{{ $judulHalaman }}</h1>
    <p class="intro">Kenali program kerja dan kegiatan HIMATIF yang tersedia.</p>

    @if (($himatifAccount['role'] ?? null) === 'pengurus')
        <div class="toolbar"><span>Kelola program kerja HIMATIF.</span><a class="button button-primary" href="{{ route('kegiatan.create') }}">+ Tambah Program Kerja</a></div>
    @endif

    @if (count($daftarKegiatan) === 0)
        <p class="empty">Belum ada program kerja yang ditampilkan.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Kode</th><th>Nama Kegiatan</th><th>Kategori</th><th>Deskripsi Singkat</th><th>Tanggal</th><th>Lokasi</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($daftarKegiatan as $kegiatan)
                        <tr>
                            <td>{{ $kegiatan->getKode() }}</td>
                            <td><strong>{{ $kegiatan->getNama() }}</strong></td>
                            <td>{{ $kegiatan->getKategori() }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($kegiatan->getDeskripsi(), 100) }}</td>
                            <td>{{ $kegiatan->getTanggalTampil() }}</td>
                            <td>{{ $kegiatan->getLokasi() }}</td>
                            <td><span class="badge {{ $kegiatan->getStatus() === 'Akan datang' ? 'upcoming' : 'done' }}">{{ $kegiatan->getStatus() }}</span></td>
                            <td>
                                <div class="table-actions">
                                    <a class="button button-small" href="{{ route('kegiatan.show', $kegiatan->getId()) }}">Detail</a>
                                    @if (($himatifAccount['role'] ?? null) === 'pengurus')
                                        <a class="button button-small" href="{{ route('kegiatan.edit', $kegiatan->getId()) }}">Edit</a>
                                        <form method="POST" action="{{ route('kegiatan.destroy', $kegiatan->getId()) }}" onsubmit="return confirm('Hapus program kerja ini?');">@csrf @method('DELETE')<button class="button button-small button-danger" type="submit">Hapus</button></form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection