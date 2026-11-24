@extends('layouts.auth')

@section('content')
    <h2>Login Anggota HIMATIF</h2>
    <p class="muted">Gunakan email dan password akun anggota yang sudah diverifikasi.</p>
    @if ($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="form-group"><label for="password">Password</label><div class="password-row"><input id="password" type="password" name="password" autocomplete="current-password" required><button class="button" type="button" data-password-toggle="password" aria-label="Tampilkan password">Lihat</button></div>@error('password')<p class="field-error">{{ $message }}</p>@enderror</div>
        <button class="button button-primary" style="width:100%;min-height:46px" type="submit">Login</button>
    </form>
    <div class="link-row"><a href="{{ route('register') }}">Registrasi anggota</a><a href="{{ route('home') }}">Kembali ke website</a></div>
    <script>document.querySelectorAll('[data-password-toggle]').forEach((button)=>{button.addEventListener('click',()=>{const input=document.getElementById(button.dataset.passwordToggle);const visible=input.type==='password';input.type=visible?'text':'password';button.textContent=visible?'Sembunyikan':'Lihat';button.setAttribute('aria-label',visible?'Sembunyikan password':'Tampilkan password')})})</script>
@endsection