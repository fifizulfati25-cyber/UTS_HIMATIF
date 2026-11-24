<?php

namespace App\Domain\Himatif;

class Foto
{
    public function __construct(private ?string $file, private string $keterangan) {}

    public function tersedia(): bool
    {
        return $this->file !== null && $this->file !== '';
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function getKeterangan(): string
    {
        return $this->keterangan;
    }
}
