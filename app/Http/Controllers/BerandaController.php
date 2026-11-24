<?php

namespace App\Http\Controllers;

use App\Services\HimatifSessionStore;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function __construct(private HimatifSessionStore $store) {}

    public function index(Request $request): View
    {
        $judulHalaman = 'HIMATIF — Himpunan Mahasiswa Teknik Informatika';
        $programUnggulan = array_slice($this->store->kegiatan(), 0, 3);
        $profil = config('himatif_profile');
        $visi = $profil['visi'];
        $misi = $profil['misi'];
        $account = $request->attributes->get('himatifAccount');

        return view('home', compact('judulHalaman', 'programUnggulan', 'account', 'visi', 'misi'));
    }
}
