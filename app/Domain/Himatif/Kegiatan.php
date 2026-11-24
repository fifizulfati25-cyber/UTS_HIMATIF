<?php

namespace App\Domain\Himatif;

use Carbon\Carbon;
use InvalidArgumentException;

class Kegiatan
{
    public function __construct(
        private string $id,
        private string $kode,
        private string $nama,
        private string $kategori,
        private string $tanggal,
        private string $lokasi,
        private string $status,
        private string $deskripsi
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['id'],
            (string) ($data['kode'] ?? sprintf('KGT-%03d', (int) $data['id'])),
            (string) $data['nama'],
            (string) ($data['kategori'] ?? 'Program Kerja'),
            self::normalisasiTanggal((string) $data['tanggal']),
            (string) $data['lokasi'],
            (string) $data['status'],
            (string) $data['deskripsi']
        );
    }

    private static function normalisasiTanggal(string $tanggal): string
    {
        $bulan = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
        ];

        if (preg_match('/^(\d{1,2})\s+([[:alpha:]]+)\s+(\d{4})$/u', trim($tanggal), $bagian) === 1) {
            $nomorBulan = $bulan[mb_strtolower($bagian[2])] ?? null;
            if ($nomorBulan !== null) {
                return sprintf('%04d-%s-%02d', (int) $bagian[3], $nomorBulan, (int) $bagian[1]);
            }
        }

        try {
            return Carbon::parse($tanggal)->format('Y-m-d');
        } catch (\Throwable) {
            throw new InvalidArgumentException('Tanggal kegiatan tidak dapat dibaca.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getKode(): string
    {
        return $this->kode;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getKategori(): string
    {
        return $this->kategori;
    }

    public function getTanggal(): string
    {
        return $this->tanggal;
    }

    public function getTanggalTampil(): string
    {
        return Carbon::parse($this->tanggal)->locale('id')->translatedFormat('d F Y');
    }

    public function getLokasi(): string
    {
        return $this->lokasi;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDeskripsi(): string
    {
        return $this->deskripsi;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'tanggal' => $this->tanggal,
            'lokasi' => $this->lokasi,
            'status' => $this->status,
            'deskripsi' => $this->deskripsi,
        ];
    }
}
