<?php

namespace App\Http\Controllers;

use App\Services\AttendanceStore;
use App\Services\HimatifSessionStore;
use App\Services\MeetingStore;
use App\Services\MemberAccountStore;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function __construct(
        private HimatifSessionStore $sessionStore,
        private MeetingStore $meetings,
        private AttendanceStore $attendance,
        private MemberAccountStore $accounts
    ) {}

    public function dashboard(Request $request): View
    {
        $account = $request->attributes->get('himatifAccount');
        $rapatMendatang = array_values(array_filter(
            $this->meetings->all(),
            static fn (array $meeting): bool => $meeting['tanggal'] >= now()->toDateString()
        ));
        $riwayat = $this->attendance->forMember($account['nim']);
        $riwayatTerbaru = array_slice(array_reverse($riwayat), 0, 3);
        $jumlahKegiatan = count($this->sessionStore->kegiatan());
        $jumlahMenunggu = 0;
        if ($account['role'] === 'pengurus') {
            $jumlahMenunggu = count(array_filter(
                $this->accounts->all(),
                static fn (array $member): bool => $member['role'] === 'anggota' && $member['status_keanggotaan'] === 'pending'
            ));
        }
        $judulHalaman = 'Dashboard Anggota HIMATIF';

        return view('portal.dashboard', compact(
            'account', 'rapatMendatang', 'riwayatTerbaru', 'jumlahKegiatan', 'jumlahMenunggu', 'judulHalaman'
        ));
    }
}
