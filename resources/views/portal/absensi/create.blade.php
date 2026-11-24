@extends('layouts.portal')

@section('content')
    <p class="eyebrow">Area Anggota</p><h1 class="page-heading">{{ $judulHalaman }}</h1><p class="intro">Pilih rapat yang status absensinya dibuka. Identitas diambil otomatis dari akun yang sedang login.</p>
    @if ($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif
    @php($adaRapatTerbuka = count(array_filter($daftarRapat, static fn (array $rapat): bool => $rapat['status'] === 'Dibuka')) > 0)
    @if (! $adaRapatTerbuka)<p class="empty">Belum ada sesi absensi yang dibuka oleh pengurus.</p>@else
        <form class="form-card" method="POST" action="{{ route('portal.absensi.store') }}">@csrf
            <p class="notice notice-info">Mengisi absensi sebagai <strong>{{ $account['nama'] }}</strong> · {{ $account['nim'] }}.</p>
            <div class="form-group"><label for="rapat_id">Pilih rapat</label><select id="rapat_id" name="rapat_id" required><option value="">-- Pilih rapat yang dibuka --</option>@foreach ($daftarRapat as $rapat)<option value="{{ $rapat['id'] }}" @disabled($rapat['status'] !== 'Dibuka' || in_array($rapat['id'], $rapatYangSudahDiikuti, true)) @selected(old('rapat_id') === $rapat['id'])>{{ $rapat['judul'] }} · {{ \Carbon\Carbon::parse($rapat['tanggal'])->locale('id')->translatedFormat('d F Y') }} · {{ $rapat['status'] }}{{ in_array($rapat['id'], $rapatYangSudahDiikuti, true) ? ' · Sudah diisi' : '' }}</option>@endforeach</select>@error('rapat_id')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div class="form-actions"><button class="button button-primary" type="submit">Catat Kehadiran</button><a class="button" href="{{ route('portal.dashboard') }}">Kembali</a></div>
        </form>
    @endif
@endsection