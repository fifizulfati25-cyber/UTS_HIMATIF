<?php

namespace App\Http\Controllers;

use App\Services\MemberAccountStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengurusAnggotaController extends Controller
{
    public function __construct(private MemberAccountStore $accounts) {}

    public function index(): View
    {
        $daftarAnggota = array_values(array_filter(
            $this->accounts->publicAccounts(),
            static fn (array $account): bool => $account['role'] === 'anggota'
        ));
        $anggotaMenunggu = array_values(array_filter(
            $daftarAnggota,
            static fn (array $account): bool => $account['status_keanggotaan'] === 'pending'
        ));
        $anggotaTerverifikasi = array_values(array_filter(
            $daftarAnggota,
            static fn (array $account): bool => $account['status_keanggotaan'] === 'verified'
        ));
        $judulHalaman = 'Verifikasi Anggota HIMATIF';

        return view('portal.pengurus.anggota', compact('anggotaMenunggu', 'anggotaTerverifikasi', 'judulHalaman'));
    }

    public function approve(string $id): RedirectResponse
    {
        abort_unless($this->accounts->approveAnggota($id), 404);

        return redirect()->route('pengurus.anggota.index')->with('success', 'Keanggotaan berhasil diverifikasi.');
    }
}
