<?php

namespace App\Services;

use App\Domain\Himatif\Kegiatan;

class HimatifSessionStore
{
    public function kegiatan(): array
    {
        if (! session()->has('daftarKegiatan')) {
            session()->put('daftarKegiatan', $this->kegiatanContoh());
        }

        $records = session('daftarKegiatan', []);
        $normalized = array_map(static fn (array $record): array => Kegiatan::fromArray($record)->toArray(), $records);
        if ($normalized !== $records) {
            session()->put('daftarKegiatan', $normalized);
        }
        if (! session()->has('idKegiatanBerikutnya')) {
            $nextId = $normalized === [] ? 1 : max(array_map(static fn (array $record): int => (int) $record['id'], $normalized)) + 1;
            session()->put('idKegiatanBerikutnya', $nextId);
        }

        return $normalized;
    }

    public function simpanKegiatan(array $records): void
    {
        session()->put('daftarKegiatan', $records);
    }

    public function kegiatanContoh(): array
    {
        return [
            ['id' => '1', 'kode' => 'KGT-001', 'nama' => 'Rapat Kerja HIMATIF', 'kategori' => 'Rapat', 'tanggal' => '2026-10-24', 'lokasi' => 'Ruang Kelas', 'status' => 'Selesai', 'deskripsi' => 'Membahas rencana kegiatan dan program kerja HIMATIF.'],
            ['id' => '2', 'kode' => 'KGT-002', 'nama' => 'Python Bootcamp', 'kategori' => 'Pelatihan', 'tanggal' => '2026-11-15', 'lokasi' => 'Ruang Kelas', 'status' => 'Akan datang', 'deskripsi' => 'Pelatihan dasar pemrograman Python untuk mahasiswa, dari sintaks dasar sampai latihan sederhana.'],
            ['id' => '3', 'kode' => 'KGT-003', 'nama' => 'Kenalan dengan Pengurus HIMATIF', 'kategori' => 'Pengenalan', 'tanggal' => '2026-11-20', 'lokasi' => 'Ruang Kelas', 'status' => 'Akan datang', 'deskripsi' => 'Pengenalan susunan pengurus HIMATIF beserta tugasnya, sekaligus sesi tanya jawab dengan anggota.'],
            ['id' => '4', 'kode' => 'KGT-004', 'nama' => 'Bakti Sosial', 'kategori' => 'Sosial', 'tanggal' => '2026-09-05', 'lokasi' => 'Desa Cibiru', 'status' => 'Selesai', 'deskripsi' => 'Kegiatan sosial mahasiswa bersama warga sekitar.'],
            ['id' => '5', 'kode' => 'KGT-005', 'nama' => 'Sharing Session Alumni', 'kategori' => 'Diskusi', 'tanggal' => '2026-12-05', 'lokasi' => 'Convention Hall Kampus 2', 'status' => 'Akan datang', 'deskripsi' => 'Berbagi pengalaman kuliah dan pekerjaan bersama alumni.'],
        ];
    }
}
