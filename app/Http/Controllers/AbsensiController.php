<?php

namespace App\Http\Controllers;

use App\Domain\Himatif\Absensi;
use App\Domain\Himatif\Anggota;
use App\Domain\Himatif\Rapat;
use App\Services\AttendanceStore;
use App\Services\MeetingStore;
use App\Services\MemberAccountStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function __construct(
        private AttendanceStore $attendance,
        private MeetingStore $meetings,
        private MemberAccountStore $accounts
    ) {}

    public function index(Request $request): View
    {
        $account = $request->attributes->get('himatifAccount');
        $daftarAbsensi = $this->attendance->forMember($account['nim']);
        $judulHalaman = 'Riwayat Kehadiran Saya';

        return view('portal.absensi.index', compact('daftarAbsensi', 'judulHalaman'));
    }

    public function create(Request $request): View
    {
        $account = $request->attributes->get('himatifAccount');
        $daftarRapat = $this->meetings->all();
        $rapatYangSudahDiikuti = array_column($this->attendance->forMember($account['nim']), 'rapat_id');
        $adaRapatTerbuka = count(array_filter($daftarRapat, static fn (array $rapat): bool => $rapat['status'] === 'Dibuka')) > 0;
        $judulHalaman = 'Isi Absensi Rapat';

        return view('portal.absensi.create', compact('account', 'daftarRapat', 'rapatYangSudahDiikuti', 'adaRapatTerbuka', 'judulHalaman'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['rapat_id' => ['required', 'string', 'max:20']], ['rapat_id.required' => 'Pilih rapat yang ingin diikuti.']);
        $account = $request->attributes->get('himatifAccount');
        $rapatData = $this->meetings->find($data['rapat_id']);
        if ($rapatData === null) {
            return back()->withInput()->with('error', 'Rapat yang dipilih tidak ditemukan.');
        }

        $anggota = new Anggota($account['nim'], $account['nama'], (bool) $account['aktif']);
        $rapat = Rapat::fromArray($rapatData);
        $sudahAbsen = Absensi::cekSudahAbsen($this->attendance->all(), $anggota->getNim(), $rapat->getId());
        $alasan = $anggota->alasanTidakBisaAbsen($rapat, $sudahAbsen);
        if ($alasan !== null) {
            return back()->withInput()->with('error', $alasan);
        }

        $record = Absensi::catat('0', $anggota, $rapat, now()->format('d-m-Y H:i'))->toArray();
        $record['tanggal_rapat'] = $rapat->getTanggal();
        $result = $this->attendance->record($record);
        if (! $result['created']) {
            return back()->withInput()->with('error', 'Kamu sudah mengisi absensi untuk rapat ini.');
        }

        return redirect()->route('portal.absensi.index')->with('success', 'Kehadiran berhasil dicatat.');
    }

    public function show(Request $request, string $id): Response
    {
        $account = $request->attributes->get('himatifAccount');
        $absensi = $this->attendance->findForMember($id, $account['nim']);
        $judulHalaman = 'Detail Kehadiran';

        return response()->view('portal.absensi.show', compact('absensi', 'id', 'judulHalaman'), $absensi === null ? 404 : 200);
    }

    public function rekap(): View
    {
        $daftarAbsensi = $this->attendance->all();
        $daftarRapat = $this->meetings->all();
        $rapatTerhitung = [];
        foreach ($daftarRapat as $rapat) {
            $rapatTerhitung[] = array_merge($rapat, ['jumlah_hadir' => count(array_filter(
                $daftarAbsensi,
                static fn (array $attendance): bool => $attendance['rapat_id'] === $rapat['id']
            ))]);
        }

        $rekapAnggota = [];
        foreach ($this->accounts->all() as $account) {
            if ($account['role'] !== 'anggota' || $account['status_keanggotaan'] !== 'verified') {
                continue;
            }
            $rekapAnggota[] = [
                'nim' => $account['nim'],
                'nama' => $account['nama'],
                'jumlah_hadir' => count(array_filter($daftarAbsensi, static fn (array $attendance): bool => $attendance['nim'] === $account['nim'])),
            ];
        }

        $judulHalaman = 'Rekap Absensi Anggota';

        return view('portal.pengurus.absensi', compact('rapatTerhitung', 'daftarAbsensi', 'rekapAnggota', 'judulHalaman'));
    }
}
