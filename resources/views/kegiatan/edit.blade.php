@extends('layouts.app')

@section('content')
    <h1>{{ $judulHalaman }}</h1>
    <p class="intro">Perbarui informasi program kerja.</p>
    <form class="form-card" method="POST" action="{{ route('kegiatan.update', $kegiatan->getId()) }}">
        @csrf
        @method('PUT')
        @include('kegiatan._form')
        <div class="form-actions">
            <button class="button button-primary" type="submit">Simpan Perubahan</button>
            <a class="button" href="{{ route('kegiatan.index') }}">Batal</a>
        </div>
    </form>
@endsection