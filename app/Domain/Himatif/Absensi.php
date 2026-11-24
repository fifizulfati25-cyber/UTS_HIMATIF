<?php

namespace App\Domain\Himatif;

class Absensi
{
    public function __construct(
        private string $id,
        private string $nimAnggota,
        private string $namaAnggota,
        private string $idRapat,
        private string $judulRapat,
        private string $waktuHadir
    ) {}

    public static function catat(string $id, Anggota $anggota, Rapat $rapat, string $waktuHadir): self
    {
        return new self($id, $anggota->getNim(), $anggota->getNama(), $rapat->getId(), $rapat->getJudul(), $waktuHadir);
    }

    public static function cekSudahAbsen(array $daftarAbsensi, string $nim, string $rapatId): bool
    {
        foreach ($daftarAbsensi as $absensi) {
            if ($absensi['nim'] === $nim && $absensi['rapat_id'] === $rapatId) {
                return true;
            }
        }

        return false;
    }

    public static function fromArray(array $data): self
    {
        return new self((string) $data['id'], (string) $data['nim'], (string) $data['nama'], (string) $data['rapat_id'], (string) $data['rapat'], (string) $data['waktu_hadir']);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nim' => $this->nimAnggota,
            'nama' => $this->namaAnggota,
            'rapat_id' => $this->idRapat,
            'rapat' => $this->judulRapat,
            'waktu_hadir' => $this->waktuHadir,
        ];
    }
}
