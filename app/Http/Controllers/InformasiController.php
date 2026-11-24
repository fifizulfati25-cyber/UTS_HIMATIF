<?php

namespace App\Http\Controllers;

use App\Domain\Himatif\Foto;
use App\Domain\Himatif\KontakPerson;
use App\Services\HimatifSessionStore;
use Illuminate\View\View;

class InformasiController extends Controller
{
    public function __construct(private HimatifSessionStore $store) {}

    public function profil(): View
    {
        $judulHalaman = 'Profil HIMATIF';
        $profil = config('himatif_profile');
        $periode = $profil['periode'];
        $visi = $profil['visi'];
        $misi = $profil['misi'];
        $bph = $profil['bph'];
        $bidang = $profil['bidang'];

        return view('informasi.profil', compact('judulHalaman', 'periode', 'visi', 'misi', 'bph', 'bidang'));
    }

    public function galeri(): View
    {
        $judulHalaman = 'Galeri Kegiatan HIMATIF';
        $daftarGaleri = [];
        foreach ($this->store->kegiatan() as $kegiatan) {
            $foto = new Foto(null, 'Foto dokumentasi belum tersedia di project.');
            $daftarGaleri[] = ['nama' => $kegiatan['nama'], 'tanggal' => $kegiatan['tanggal'], 'foto' => $foto];
        }

        return view('informasi.galeri', compact('judulHalaman', 'daftarGaleri'));
    }

    public function kontak(): View
    {
        $judulHalaman = 'Kontak HIMATIF';
        $kontak = new KontakPerson('Pengurus HIMATIF', 'Kontak organisasi', 'Kontak resmi belum tersedia. Silakan lengkapi dari informasi pengurus.');

        return view('informasi.kontak', compact('judulHalaman', 'kontak'));
    }
}
