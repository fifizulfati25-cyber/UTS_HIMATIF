<?php

namespace App\Http\Controllers;

use App\Services\MemberAccountStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private MemberAccountStore $accounts) {}

    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->attributes->get('himatifAccount') !== null) {
            return redirect()->route('portal.dashboard');
        }

        return view('auth.login', ['judulHalaman' => 'Login Anggota HIMATIF']);
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if ($request->attributes->get('himatifAccount') !== null) {
            return redirect()->route('portal.dashboard');
        }

        return view('auth.register', ['judulHalaman' => 'Registrasi Anggota HIMATIF']);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'min:2', 'max:100'],
            'nim' => ['required', 'string', 'regex:/^[0-9]{5,20}$/'],
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nim.regex' => 'NIM harus berisi 5 sampai 20 angka.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $result = $this->accounts->registerAnggota([
            'nama' => $data['nama'],
            'nim' => trim($data['nim']),
            'email' => mb_strtolower(trim($data['email'])),
            'password_hash' => Hash::make($data['password']),
        ]);
        if (! $result['created']) {
            $message = $result['field'] === 'email'
                ? 'Email tersebut sudah digunakan.'
                : 'NIM tersebut sudah terdaftar.';

            return back()->withInput($request->only('nama', 'nim', 'email'))->withErrors([$result['field'] => $message]);
        }

        return redirect()->route('login')->with('success', 'Pendaftaran tersimpan. Silakan menunggu verifikasi pengurus sebelum masuk ke portal absensi.');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $email = mb_strtolower(trim($data['email']));
        $account = $this->accounts->findByEmail($email);
        if ($account === null || ! Hash::check($data['password'], $account['password_hash'])) {
            return back()->withInput(['email' => $email])->withErrors(['email' => 'Email atau password tidak cocok.']);
        }
        if ($account['status_keanggotaan'] !== 'verified' || ! $account['aktif']) {
            return back()->withInput(['email' => $email])->withErrors(['email' => 'Akun belum disetujui atau sedang tidak aktif.']);
        }

        $request->session()->regenerate();
        $request->session()->put('himatif_account_id', $account['id']);

        return redirect()->intended(route('portal.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $preserved = $request->session()->only([
            'daftarKegiatan',
            'idKegiatanBerikutnya',
            'himatif_anggota',
            'himatif_rapat',
            'himatif_absensi',
            'himatif_id_absensi_berikutnya',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put($preserved);

        return redirect()->route('home')->with('success', 'Kamu sudah keluar dari Portal Anggota.');
    }
}
