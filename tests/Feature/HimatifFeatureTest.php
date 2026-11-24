<?php

namespace Tests\Feature;

use App\Domain\Himatif\Anggota;
use App\Domain\Himatif\BisaAbsen;
use App\Domain\Himatif\Pengguna;
use App\Domain\Himatif\PengurusHimatif;
use App\Domain\Himatif\Rapat;
use Tests\TestCase;

class HimatifFeatureTest extends TestCase
{
    public function test_information_pages_are_available_without_login(): void
    {
        $this->get(route('profil.index'))->assertOk()->assertSee('Visi Kami')->assertSee('Badan Pengurus Harian (BPH)')->assertSee('Muhamad Marlo Putra Sagara');
        $this->get(route('galeri.index'))->assertOk();
        $this->get(route('kontak.index'))->assertOk()->assertSee('Kontak resmi belum tersedia');
        $this->get(route('kegiatan.index'))->assertOk()->assertSee('Rapat Kerja HIMATIF');
    }

    public function test_domain_inheritance_interface_overriding_and_attendance_rules_work(): void
    {
        $member = new Anggota('2310001', 'Anggota Tes');
        $meeting = new Rapat('R-001', 'Rapat Tes', '2026-11-24', 'Ruang Kelas', 'Agenda', true);
        $this->assertInstanceOf(Pengguna::class, $member);
        $this->assertInstanceOf(BisaAbsen::class, $member);
        $this->assertTrue($member->login());
        $this->assertSame('Anggota Tes (NIM 2310001) - Anggota HIMATIF - Aktif', $member->tampilkanProfil());
        $this->assertNull($member->alasanTidakBisaAbsen($meeting, false));
        $this->assertCount(0, $member->lihatRiwayatHadir([]));
        $member->nonaktifkan();
        $this->assertFalse($member->login());
        $this->assertSame('Anggota berstatus tidak aktif sehingga tidak dapat mengisi absensi.', $member->alasanTidakBisaAbsen($meeting, false));

        $manager = new PengurusHimatif('2399001', 'Pengurus Tes', 'Pengurus HIMATIF');
        $manager->ubahStatusRapat($meeting);
        $this->assertFalse($meeting->isDibuka());
    }
}
