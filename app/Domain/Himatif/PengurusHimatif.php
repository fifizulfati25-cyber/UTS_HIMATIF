<?php

namespace App\Domain\Himatif;

class PengurusHimatif extends Pengguna
{
    public function __construct(string $id, string $nama, private string $jabatan)
    {
        parent::__construct($id, $nama);
    }

    public function getJabatan(): string
    {
        return $this->jabatan;
    }

    public function aksesUtama(): string
    {
        return 'Mengelola jadwal rapat dan memantau rekap kehadiran.';
    }

    public function tampilkanProfil(): string
    {
        return parent::tampilkanProfil().' - Pengurus ('.$this->jabatan.')';
    }

    public function ubahStatusRapat(Rapat $rapat): void
    {
        if ($rapat->isDibuka()) {
            $rapat->tutup();

            return;
        }

        $rapat->buka();
    }
}
