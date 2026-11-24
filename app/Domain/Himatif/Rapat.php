<?php

namespace App\Domain\Himatif;

class Rapat
{
    public function __construct(
        private string $id,
        private string $judul,
        private string $tanggal,
        private string $lokasi,
        private string $deskripsi,
        private bool $dibuka = false
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getJudul(): string
    {
        return $this->judul;
    }

    public function getTanggal(): string
    {
        return $this->tanggal;
    }

    public function getLokasi(): string
    {
        return $this->lokasi;
    }

    public function getDeskripsi(): string
    {
        return $this->deskripsi;
    }

    public function isDibuka(): bool
    {
        return $this->dibuka;
    }

    public function getStatus(): string
    {
        return $this->dibuka ? 'Dibuka' : 'Ditutup';
    }

    public function cekStatus(): string
    {
        return $this->getStatus();
    }

    public function buka(): void
    {
        $this->dibuka = true;
    }

    public function tutup(): void
    {
        $this->dibuka = false;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['id'],
            (string) $data['judul'],
            (string) $data['tanggal'],
            (string) $data['lokasi'],
            (string) $data['deskripsi'],
            ($data['status'] ?? 'Ditutup') === 'Dibuka'
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'tanggal' => $this->tanggal,
            'lokasi' => $this->lokasi,
            'deskripsi' => $this->deskripsi,
            'status' => $this->getStatus(),
        ];
    }
}
