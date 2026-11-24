<?php

namespace App\Domain\Himatif;

abstract class Pengguna
{
    public function __construct(
        protected string $id,
        protected string $nama
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function tampilkanProfil(): string
    {
        return $this->nama.' ('.$this->id.')';
    }

    abstract public function aksesUtama(): string;
}
