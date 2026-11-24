<?php

namespace App\Http\Controllers;

use App\Domain\Himatif\Kegiatan;
use App\Services\HimatifSessionStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KegiatanController extends Controller
{
    public function __construct(private HimatifSessionStore $store) {}

    private function dataKegiatan(): array
    {
        return $this->store->kegiatan();
    }

    private function cariKegiatan(string $id): ?Kegiatan
    {
        foreach ($this->dataKegiatan() as $kegiatan) {
            if ((string) $kegiatan['id'] === $id) {
                return Kegiatan::fromArray($kegiatan);
            }
        }

        return null;
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:20'],
            'nama' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'string', 'max:50'],
            'tanggal' => ['required', 'date'],
            'lokasi' => ['required', 'string', 'max:150'],
            'status' => ['required', Rule::in(['Akan datang', 'Selesai'])],
            'deskripsi' => ['required', 'string', 'max:1000'],
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'string' => 'Kolom :attribute harus berupa teks.',
            'max' => 'Kolom :attribute maksimal :max karakter.',
            'date' => 'Tanggal harus berupa tanggal yang valid.',
            'status.in' => 'Status harus dipilih sebagai Akan datang atau Selesai.',
        ], [
            'kode' => 'kode kegiatan', 'nama' => 'nama kegiatan', 'kategori' => 'kategori',
            'tanggal' => 'tanggal', 'lokasi' => 'lokasi', 'status' => 'status', 'deskripsi' => 'deskripsi',
        ]);
    }

    private function kodeSudahDigunakan(string $kode, ?string $abaikanId = null): bool
    {
        foreach ($this->dataKegiatan() as $kegiatan) {
            if ((string) $kegiatan['id'] !== $abaikanId
                && mb_strtolower(trim($kegiatan['kode'])) === mb_strtolower(trim($kode))) {
                return true;
            }
        }

        return false;
    }

    private function buatKegiatan(string $id, array $data): Kegiatan
    {
        return new Kegiatan($id, trim($data['kode']), trim($data['nama']), trim($data['kategori']), $data['tanggal'], trim($data['lokasi']), $data['status'], trim($data['deskripsi']));
    }

    public function index(): View
    {
        $daftarKegiatan = array_map(static fn (array $data): Kegiatan => Kegiatan::fromArray($data), $this->dataKegiatan());
        $judulHalaman = 'Program Kerja HIMATIF';

        return view('kegiatan.index', compact('daftarKegiatan', 'judulHalaman'));
    }

    public function create(): View
    {
        $judulHalaman = 'Tambah Program Kerja';
        $kegiatan = null;

        return view('kegiatan.create', compact('judulHalaman', 'kegiatan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        if ($this->kodeSudahDigunakan($data['kode'])) {
            return back()->withInput()->withErrors(['kode' => 'Kode kegiatan sudah digunakan. Pilih kode yang berbeda.']);
        }

        $records = $this->dataKegiatan();
        $id = (int) session('idKegiatanBerikutnya', 1);
        $records[] = $this->buatKegiatan((string) $id, $data)->toArray();
        $this->store->simpanKegiatan($records);
        session()->put('idKegiatanBerikutnya', $id + 1);

        return redirect()->route('kegiatan.index')->with('success', 'Program kerja berhasil ditambahkan.');
    }

    public function show(string $id): Response
    {
        $kegiatan = $this->cariKegiatan($id);
        $judulHalaman = $kegiatan?->getNama() ?? 'Program kerja tidak ditemukan';

        return response()->view('kegiatan.show', compact('kegiatan', 'id', 'judulHalaman'), $kegiatan === null ? 404 : 200);
    }

    public function edit(string $id): View
    {
        $kegiatan = $this->cariKegiatan($id);
        abort_if($kegiatan === null, 404);
        $judulHalaman = 'Edit Program Kerja';

        return view('kegiatan.edit', compact('kegiatan', 'judulHalaman'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        abort_if($this->cariKegiatan($id) === null, 404);
        $data = $this->validasi($request);
        if ($this->kodeSudahDigunakan($data['kode'], $id)) {
            return back()->withInput()->withErrors(['kode' => 'Kode kegiatan sudah digunakan. Pilih kode yang berbeda.']);
        }

        $records = $this->dataKegiatan();
        foreach ($records as $index => $kegiatan) {
            if ((string) $kegiatan['id'] === $id) {
                $records[$index] = $this->buatKegiatan($id, $data)->toArray();
                $this->store->simpanKegiatan($records);

                return redirect()->route('kegiatan.index')->with('success', 'Program kerja berhasil diperbarui.');
            }
        }

        abort(404);
    }

    public function destroy(string $id): RedirectResponse
    {
        $records = $this->dataKegiatan();
        $jumlahSebelum = count($records);
        $records = array_values(array_filter($records, static fn (array $kegiatan): bool => (string) $kegiatan['id'] !== $id));
        abort_if(count($records) === $jumlahSebelum, 404);
        $this->store->simpanKegiatan($records);

        return redirect()->route('kegiatan.index')->with('success', 'Program kerja berhasil dihapus.');
    }
}
