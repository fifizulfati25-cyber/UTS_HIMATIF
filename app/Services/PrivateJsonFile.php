<?php

namespace App\Services;

use Closure;
use JsonException;
use RuntimeException;

class PrivateJsonFile
{
    public function __construct(private string $path) {}

    public function exists(): bool
    {
        return is_file($this->path) || is_file($this->path.'.previous');
    }

    public function read(): array
    {
        return $this->access(static fn (array $records): array => [$records, $records]);
    }

    public function update(Closure $callback): mixed
    {
        return $this->access($callback);
    }

    private function access(Closure $callback): mixed
    {
        $directory = dirname($this->path);
        if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
            throw new RuntimeException('Direktori penyimpanan privat tidak dapat dibuat.');
        }
        @chmod($directory, 0700);

        $lockPath = $this->path.'.lock';
        $lock = fopen($lockPath, 'c+');
        if ($lock === false) {
            throw new RuntimeException('File lock penyimpanan privat tidak dapat dibuka.');
        }
        @chmod($lockPath, 0600);

        if (! flock($lock, LOCK_EX)) {
            fclose($lock);
            throw new RuntimeException('Penyimpanan privat sedang tidak dapat dikunci.');
        }

        try {
            $this->recoverInterruptedWrite();
            $records = $this->readRecords();
            [$updatedRecords, $result] = $callback($records);
            if ($updatedRecords !== $records) {
                $this->writeRecordsAtomically($updatedRecords);
            }

            return $result;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function readRecords(): array
    {
        if (! is_file($this->path)) {
            return [];
        }

        $contents = file_get_contents($this->path);
        if ($contents === false) {
            throw new RuntimeException('Data penyimpanan privat tidak dapat dibaca.');
        }
        if (trim($contents) === '') {
            return [];
        }

        try {
            $records = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Format data penyimpanan privat rusak.', previous: $exception);
        }
        if (! is_array($records) || ! array_is_list($records)) {
            throw new RuntimeException('Format data penyimpanan privat tidak sesuai.');
        }

        return $records;
    }

    private function writeRecordsAtomically(array $records): void
    {
        $temporaryPath = $this->path.'.tmp.'.bin2hex(random_bytes(8));
        $previousPath = $this->path.'.previous';
        $contents = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL;
        $temporary = fopen($temporaryPath, 'xb');
        if ($temporary === false) {
            throw new RuntimeException('File sementara penyimpanan privat tidak dapat dibuat.');
        }

        try {
            $remaining = $contents;
            while ($remaining !== '') {
                $written = fwrite($temporary, $remaining);
                if ($written === false || $written === 0) {
                    throw new RuntimeException('Data penyimpanan privat tidak dapat ditulis seluruhnya.');
                }
                $remaining = substr($remaining, $written);
            }
            if (! fflush($temporary)) {
                throw new RuntimeException('Data penyimpanan privat gagal disimpan.');
            }
            if (function_exists('fsync')) {
                @fsync($temporary);
            }
        } catch (RuntimeException $exception) {
            fclose($temporary);
            @unlink($temporaryPath);
            throw $exception;
        }
        fclose($temporary);
        @chmod($temporaryPath, 0600);

        if (is_file($this->path)) {
            if (is_file($previousPath) && ! unlink($previousPath)) {
                @unlink($temporaryPath);
                throw new RuntimeException('File cadangan sementara tidak dapat dibersihkan.');
            }
            if (! rename($this->path, $previousPath)) {
                @unlink($temporaryPath);
                throw new RuntimeException('File lama tidak dapat diamankan sebelum pembaruan.');
            }
        }

        if (! rename($temporaryPath, $this->path)) {
            if (is_file($previousPath)) {
                @rename($previousPath, $this->path);
            }
            @unlink($temporaryPath);
            throw new RuntimeException('Pembaruan penyimpanan privat gagal dipasang.');
        }
        @chmod($this->path, 0600);
        if (is_file($previousPath)) {
            @unlink($previousPath);
        }
    }

    private function recoverInterruptedWrite(): void
    {
        $previousPath = $this->path.'.previous';
        if (! is_file($this->path) && is_file($previousPath)) {
            if (! rename($previousPath, $this->path)) {
                throw new RuntimeException('Penyimpanan privat tidak dapat dipulihkan dari file cadangan.');
            }
        } elseif (is_file($this->path) && is_file($previousPath)) {
            @unlink($previousPath);
        }
    }
}
