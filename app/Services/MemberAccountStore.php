<?php

namespace App\Services;

use Illuminate\Support\Str;
use RuntimeException;

class MemberAccountStore
{
    private PrivateJsonFile $file;

    public function __construct()
    {
        $this->file = new PrivateJsonFile((string) config('himatif.accounts_path'));
    }

    public function all(): array
    {
        return $this->file->read();
    }

    public function publicAccounts(): array
    {
        return array_map($this->withoutPassword(...), $this->all());
    }

    public function findById(string $id): ?array
    {
        foreach ($this->all() as $account) {
            if ($account['id'] === $id) {
                return $account;
            }
        }

        return null;
    }

    public function findByEmail(string $email): ?array
    {
        $normalizedEmail = mb_strtolower(trim($email));
        foreach ($this->all() as $account) {
            if ($account['email'] === $normalizedEmail) {
                return $account;
            }
        }

        return null;
    }

    public function hasPengurus(): bool
    {
        foreach ($this->all() as $account) {
            if ($account['role'] === 'pengurus') {
                return true;
            }
        }

        return false;
    }

    public function registerAnggota(array $data): array
    {
        $id = (string) Str::uuid();

        return $this->file->update(function (array $accounts) use ($data, $id): array {
            foreach ($accounts as $account) {
                if ($account['email'] === $data['email']) {
                    return [$accounts, ['created' => false, 'field' => 'email']];
                }
                if ($account['nim'] === $data['nim']) {
                    return [$accounts, ['created' => false, 'field' => 'nim']];
                }
            }

            $account = [
                'id' => $id,
                'nama' => trim($data['nama']),
                'nim' => trim($data['nim']),
                'email' => mb_strtolower(trim($data['email'])),
                'password_hash' => $data['password_hash'],
                'role' => 'anggota',
                'status_keanggotaan' => 'pending',
                'aktif' => true,
                'dibuat_pada' => now()->toIso8601String(),
            ];
            $accounts[] = $account;

            return [$accounts, ['created' => true, 'account' => $this->withoutPassword($account)]];
        });
    }

    public function createInitialPengurus(array $data): array
    {
        $id = (string) Str::uuid();

        return $this->file->update(function (array $accounts) use ($data, $id): array {
            foreach ($accounts as $account) {
                if ($account['role'] === 'pengurus') {
                    throw new RuntimeException('Akun pengurus awal sudah tersedia.');
                }
                if ($account['email'] === mb_strtolower(trim($data['email']))) {
                    throw new RuntimeException('Email sudah digunakan akun lain.');
                }
                if ($account['nim'] === trim($data['nim'])) {
                    throw new RuntimeException('NIM sudah digunakan akun lain.');
                }
            }

            $account = [
                'id' => $id,
                'nama' => trim($data['nama']),
                'nim' => trim($data['nim']),
                'email' => mb_strtolower(trim($data['email'])),
                'password_hash' => $data['password_hash'],
                'role' => 'pengurus',
                'status_keanggotaan' => 'verified',
                'aktif' => true,
                'dibuat_pada' => now()->toIso8601String(),
            ];
            $accounts[] = $account;

            return [$accounts, $this->withoutPassword($account)];
        });
    }

    public function approveAnggota(string $id): bool
    {
        return $this->file->update(function (array $accounts) use ($id): array {
            foreach ($accounts as $index => $account) {
                if ($account['id'] === $id && $account['role'] === 'anggota' && $account['status_keanggotaan'] === 'pending') {
                    $accounts[$index]['status_keanggotaan'] = 'verified';

                    return [$accounts, true];
                }
            }

            return [$accounts, false];
        });
    }

    public function withoutPassword(array $account): array
    {
        unset($account['password_hash']);

        return $account;
    }
}
