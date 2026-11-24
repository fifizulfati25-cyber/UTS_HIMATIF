<?php

namespace App\Services;

class MeetingStore
{
    private PrivateJsonFile $file;

    public function __construct()
    {
        $this->file = new PrivateJsonFile((string) config('himatif.meetings_path'));
    }

    public function all(): array
    {
        if (! $this->file->exists()) {
            $this->file->update(fn (array $meetings): array => [$meetings === [] ? $this->seed() : $meetings, null]);
        }

        return $this->file->read();
    }

    public function find(string $id): ?array
    {
        foreach ($this->all() as $meeting) {
            if ($meeting['id'] === $id) {
                return $meeting;
            }
        }

        return null;
    }

    public function create(array $data): array
    {
        $this->all();

        return $this->file->update(function (array $meetings) use ($data): array {
            $nextNumber = 1;
            foreach ($meetings as $meeting) {
                if (preg_match('/^R-(\d+)$/', $meeting['id'], $matches) === 1) {
                    $nextNumber = max($nextNumber, (int) $matches[1] + 1);
                }
            }

            $record = [
                'id' => 'R-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT),
                'judul' => trim($data['judul']),
                'tanggal' => $data['tanggal'],
                'lokasi' => trim($data['lokasi']),
                'deskripsi' => trim($data['deskripsi']),
                'status' => 'Ditutup',
            ];
            $meetings[] = $record;

            return [$meetings, $record];
        });
    }

    public function update(string $id, array $data): ?array
    {
        return $this->file->update(function (array $meetings) use ($id, $data): array {
            foreach ($meetings as $index => $meeting) {
                if ($meeting['id'] === $id) {
                    $meetings[$index] = array_merge($meeting, [
                        'judul' => trim($data['judul']),
                        'tanggal' => $data['tanggal'],
                        'lokasi' => trim($data['lokasi']),
                        'deskripsi' => trim($data['deskripsi']),
                    ]);

                    return [$meetings, $meetings[$index]];
                }
            }

            return [$meetings, null];
        });
    }

    public function delete(string $id): bool
    {
        return $this->file->update(function (array $meetings) use ($id): array {
            $updated = array_values(array_filter($meetings, static fn (array $meeting): bool => $meeting['id'] !== $id));
            if (count($updated) === count($meetings)) {
                return [$meetings, false];
            }

            return [$updated, true];
        });
    }

    public function setStatus(string $id, string $status): ?array
    {
        return $this->file->update(function (array $meetings) use ($id, $status): array {
            foreach ($meetings as $index => $meeting) {
                if ($meeting['id'] === $id) {
                    $meetings[$index]['status'] = $status;

                    return [$meetings, $meetings[$index]];
                }
            }

            return [$meetings, null];
        });
    }

    private function seed(): array
    {
        return [
            [
                'id' => 'R-001', 'judul' => 'Rapat Koordinasi Mingguan', 'tanggal' => '2026-11-24',
                'lokasi' => 'Ruang Kelas', 'deskripsi' => 'Koordinasi agenda dan kebutuhan kegiatan HIMATIF.', 'status' => 'Dibuka',
            ],
            [
                'id' => 'R-002', 'judul' => 'Evaluasi Program Kerja', 'tanggal' => '2026-11-28',
                'lokasi' => 'Ruang Kelas', 'deskripsi' => 'Evaluasi pelaksanaan program kerja HIMATIF.', 'status' => 'Ditutup',
            ],
        ];
    }
}
