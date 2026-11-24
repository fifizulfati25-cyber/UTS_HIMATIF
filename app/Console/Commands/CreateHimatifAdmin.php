<?php

namespace App\Console\Commands;

use App\Services\MemberAccountStore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CreateHimatifAdmin extends Command
{
    protected $signature = 'himatif:admin:create {email?}';

    protected $description = 'Buat akun pengurus awal menggunakan input privat dari terminal.';

    public function handle(MemberAccountStore $accounts): int
    {
        if ($accounts->hasPengurus()) {
            $this->error('Akun pengurus awal sudah tersedia.');

            return self::FAILURE;
        }

        $email = mb_strtolower(trim((string) ($this->argument('email') ?: $this->ask('Email pengurus'))));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || $accounts->findByEmail($email) !== null) {
            $this->error('Email tidak valid atau sudah digunakan.');

            return self::FAILURE;
        }

        $nama = trim((string) $this->ask('Nama lengkap pengurus'));
        $nim = trim((string) $this->ask('NIM pengurus'));
        if ($nama === '' || ! preg_match('/^[0-9]{5,20}$/', $nim)) {
            $this->error('Nama wajib diisi dan NIM harus berisi 5 sampai 20 angka.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Buat password (minimal 8 karakter)');
        $confirmation = (string) $this->secret('Ulangi password');
        if (strlen($password) < 8 || ! hash_equals($password, $confirmation)) {
            $this->error('Password harus minimal 8 karakter dan kedua input harus sama.');

            return self::FAILURE;
        }

        try {
            $accounts->createInitialPengurus([
                'nama' => $nama,
                'nim' => $nim,
                'email' => $email,
                'password_hash' => Hash::make($password),
            ]);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Akun pengurus awal berhasil dibuat. Password hanya tersimpan dalam bentuk hash.');

        return self::SUCCESS;
    }
}
