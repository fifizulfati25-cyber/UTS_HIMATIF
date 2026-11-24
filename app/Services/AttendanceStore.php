<?php

namespace App\Services;

class AttendanceStore
{
    private PrivateJsonFile $file;

    public function __construct()
    {
        $this->file = new PrivateJsonFile((string) config('himatif.attendance_path'));
    }

    public function all(): array
    {
        return $this->file->read();
    }

    public function forMember(string $nim): array
    {
        return array_values(array_filter($this->all(), static fn (array $record): bool => $record['nim'] === $nim));
    }

    public function findForMember(string $id, string $nim): ?array
    {
        foreach ($this->all() as $record) {
            if ($record['id'] === $id && $record['nim'] === $nim) {
                return $record;
            }
        }

        return null;
    }

    public function record(array $data): array
    {
        return $this->file->update(function (array $records) use ($data): array {
            foreach ($records as $record) {
                if ($record['nim'] === $data['nim'] && $record['rapat_id'] === $data['rapat_id']) {
                    return [$records, ['created' => false, 'record' => null]];
                }
            }

            $nextId = $records === [] ? 1 : max(array_map(static fn (array $record): int => (int) $record['id'], $records)) + 1;
            $record = array_merge($data, ['id' => (string) $nextId]);
            $records[] = $record;

            return [$records, ['created' => true, 'record' => $record]];
        });
    }
}
