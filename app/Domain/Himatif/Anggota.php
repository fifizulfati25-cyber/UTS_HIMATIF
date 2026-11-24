<?php

namespace App\Domain\Himatif;

class Anggota extends Pengguna implements BisaAbsen
{
    public function __construct(
        string $nim,
        string $nama,
        private bool $statusAktif = true
    ) {
        parent::__construct($nim, $nama);
    }

    public function getNim(): string
    {
        return $this->id;
    }

    public function isAktif(): bool
    {
        return $this->statusAktif;
    }

    public function login(): bool
    {
        return $this->statusAktif;
    }

    public function aktifkan(): void
    {
        $this->statusAktif = true;
    }

    public function nonaktifkan(): void
    {
        $this->statusAktif = false;
    }

    public function aksesUtama(): string
    {
        return 'Melihat jadwal rapat dan mengisi absensi.';
    }

    public function tampilkanProfil(): string
    {
        return $this->nama.' (NIM '.$this->id.') - Anggota HIMATIF - '.($this->statusAktif ? 'Aktif' : 'Tidak aktif');
    }

    public function alasanTidakBisaAbsen(Rapat $rapat, bool $sudahAbsen): ?string
    {
        if (! $this->login()) {
            return 'Anggota berstatus tidak aktif sehingga tidak dapat mengisi absensi.';
        }

        if (! $rapat->isDibuka()) {
            return 'Absensi tidak dapat diisi karena rapat sedang ditutup.';
        }

        if ($sudahAbsen) {
            return 'Anggota ini sudah mengisi absensi untuk rapat tersebut.';
        }

        return null;
    }

    public function lihatRiwayatHadir(array $daftarAbsensi): array
    {
        return array_values(array_filter(
            $daftarAbsensi,
            fn (array $item): bool => $item['nim'] === $this->id
        ));
    }

    public static function fromArray(array $data): self
    {
        return new self((string) $data['nim'], (string) $data['nama'], (bool) $data['statusAktif']);
    }

    public function toArray(): array
    {
        return ['nim' => $this->id, 'nama' => $this->nama, 'statusAktif' => $this->statusAktif];
    }
}
