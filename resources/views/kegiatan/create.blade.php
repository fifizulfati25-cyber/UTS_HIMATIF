@extends('layouts.app')

@section('content')
    <h1>{{ $judulHalaman }}</h1>
    <p class="intro">Isi data program kerja HIMATIF.</p>
    <form class="form-card" method="POST" action="{{ route('kegiatan.store') }}">
        @csrf
        @include('kegiatan._form')
        <div class="form-actions">
            <button class="button button-primary" type="submit">Simpan</button>
            <a class="button" href="{{ route('kegiatan.index') }}">Batal</a>
        </div>
    </form>
@endsection