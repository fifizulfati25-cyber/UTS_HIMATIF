@extends('layouts.auth')

@section('content')
    <h2>Registrasi Anggota</h2>
    <p class="muted">Akun baru menunggu verifikasi pengurus. Registrasi tidak langsung memberikan akses absensi.</p>
    @if ($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="form-group"><label for="nama">Nama lengkap</label><input id="nama" name="nama" value="{{ old('nama') }}" autocomplete="name" maxlength="100" required>@error('nama')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="nim">NIM</label><input id="nim" name="nim" value="{{ old('nim') }}" inputmode="numeric" pattern="[0-9]{5,20}" required>@error('nim')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="password">Password</label><div class="password-row"><input id="password" type="password" name="password" minlength="8" autocomplete="new-password" required><button class="button" type="button" data-password-toggle="password">Lihat</button></div>@error('password')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="password_confirmation">Konfirmasi password</label><div class="password-row"><input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required><button class="button" type="button" data-password-toggle="password_confirmation">Lihat</button></div></div>
        <button class="button button-primary" style="width:100%;min-height:46px" type="submit">Kirim pendaftaran</button>
    </form>
    <div class="link-row"><a href="{{ route('login') }}">Sudah punya akun? Login</a><a href="{{ route('home') }}">Kembali ke website</a></div>
    <script>document.querySelectorAll('[data-password-toggle]').forEach((button)=>{button.addEventListener('click',()=>{const input=document.getElementById(button.dataset.passwordToggle);const visible=input.type==='password';input.type=visible?'text':'password';button.textContent=visible?'Sembunyikan':'Lihat'})})</script>
@endsection