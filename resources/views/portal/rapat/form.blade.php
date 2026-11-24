@extends('layouts.portal')

@section('content')
    <p class="eyebrow">Khusus Pengurus</p><h1 class="page-heading">{{ $judulHalaman }}</h1><p class="intro">Isi informasi rapat. Sesi absensi untuk rapat baru dimulai dalam keadaan ditutup.</p>
    @if ($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif
    <form class="form-card" method="POST" action="{{ $rapat ? route('pengurus.rapat.update', $rapat['id']) : route('pengurus.rapat.store') }}">
        @csrf
        @if ($rapat) @method('PUT') @endif
        <div class="form-group"><label for="judul">Judul rapat</label><input id="judul" name="judul" value="{{ old('judul', $rapat['judul'] ?? '') }}" maxlength="120" required>@error('judul')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="tanggal">Tanggal</label><input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', $rapat['tanggal'] ?? '') }}" required>@error('tanggal')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="lokasi">Lokasi</label><input id="lokasi" name="lokasi" value="{{ old('lokasi', $rapat['lokasi'] ?? '') }}" maxlength="150" required>@error('lokasi')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="deskripsi">Deskripsi</label><textarea id="deskripsi" name="deskripsi" maxlength="1000" required>{{ old('deskripsi', $rapat['deskripsi'] ?? '') }}</textarea>@error('deskripsi')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-actions"><button class="button button-primary" type="submit">{{ $rapat ? 'Simpan Perubahan' : 'Simpan Jadwal' }}</button><a class="button" href="{{ route('portal.rapat.index') }}">Batal</a></div>
    </form>
@endsection