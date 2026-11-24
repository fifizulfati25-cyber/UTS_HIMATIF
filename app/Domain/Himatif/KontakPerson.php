<?php

namespace App\Domain\Himatif;

class KontakPerson
{
    public function __construct(
        private string $nama,
        private string $jabatan,
        private string $kontak
    ) {}

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getJabatan(): string
    {
        return $this->jabatan;
    }

    public function getKontak(): string
    {
        return $this->kontak;
    }
}
