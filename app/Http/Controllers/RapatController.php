<?php

namespace App\Http\Controllers;

use App\Domain\Himatif\PengurusHimatif;
use App\Domain\Himatif\Rapat;
use App\Services\AttendanceStore;
use App\Services\MeetingStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RapatController extends Controller
{
    public function __construct(private MeetingStore $meetings, private AttendanceStore $attendance) {}

    public function index(Request $request): View
    {
        $daftarRapat = array_map(Rapat::fromArray(...), $this->meetings->all());
        $account = $request->attributes->get('himatifAccount');
        $isPengurus = $account['role'] === 'pengurus';
        $judulHalaman = 'Jadwal Rapat Internal';

        return view('portal.rapat.index', compact('daftarRapat', 'isPengurus', 'judulHalaman'));
    }

    public function create(): View
    {
        $judulHalaman = 'Tambah Jadwal Rapat';
        $rapat = null;

        return view('portal.rapat.form', compact('judulHalaman', 'rapat'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validateMeeting($request);
        $this->meetings->create($request->only('judul', 'tanggal', 'lokasi', 'deskripsi'));

        return redirect()->route('portal.rapat.index')->with('success', 'Jadwal rapat berhasil ditambahkan. Sesi absensi dimulai dalam keadaan ditutup.');
    }

    public function edit(string $id): View
    {
        $rapat = $this->meetings->find($id);
        abort_if($rapat === null, 404);
        $judulHalaman = 'Edit Jadwal Rapat';

        return view('portal.rapat.form', compact('judulHalaman', 'rapat'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        abort_if($this->meetings->find($id) === null, 404);
        $data = $this->validateMeeting($request);
        abort_if($this->meetings->update($id, $data) === null, 404);

        return redirect()->route('portal.rapat.index')->with('success', 'Informasi rapat berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $hasAttendance = count(array_filter(
            $this->attendance->all(),
            static fn (array $record): bool => $record['rapat_id'] === $id
        )) > 0;
        if ($hasAttendance) {
            return redirect()->route('portal.rapat.index')->with('error', 'Rapat tidak dapat dihapus karena sudah memiliki catatan kehadiran.');
        }
        abort_unless($this->meetings->delete($id), 404);

        return redirect()->route('portal.rapat.index')->with('success', 'Jadwal rapat berhasil dihapus.');
    }

    public function ubahStatus(Request $request, string $id): RedirectResponse
    {
        $dataRapat = $this->meetings->find($id);
        abort_if($dataRapat === null, 404);
        $rapat = Rapat::fromArray($dataRapat);
        $account = $request->attributes->get('himatifAccount');
        $pengurus = new PengurusHimatif($account['nim'], $account['nama'], 'Pengurus HIMATIF');
        $pengurus->ubahStatusRapat($rapat);
        $rapatTersimpan = $this->meetings->setStatus($id, $rapat->getStatus());
        abort_if($rapatTersimpan === null, 404);

        return redirect()->route('portal.rapat.index')->with('success', 'Status absensi rapat sekarang '.$rapatTersimpan['status'].'.');
    }

    private function validateMeeting(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:120'],
            'tanggal' => ['required', 'date'],
            'lokasi' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:1000'],
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'date' => 'Tanggal rapat harus valid.',
        ]);
    }
}
