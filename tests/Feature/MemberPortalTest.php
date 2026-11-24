<?php

namespace Tests\Feature;

use App\Services\AttendanceStore;
use App\Services\HimatifSessionStore;
use App\Services\MeetingStore;
use App\Services\MemberAccountStore;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class MemberPortalTest extends TestCase
{
    private array $paths = [];

    protected function setUp(): void
    {
        parent::setUp();
        $suffix = (string) Str::uuid();
        $this->paths = [
            storage_path('framework/testing/accounts-'.$suffix.'.json'),
            storage_path('framework/testing/meetings-'.$suffix.'.json'),
            storage_path('framework/testing/attendance-'.$suffix.'.json'),
        ];
        config([
            'himatif.accounts_path' => $this->paths[0],
            'himatif.meetings_path' => $this->paths[1],
            'himatif.attendance_path' => $this->paths[2],
            'cache.limiter' => 'array',
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->paths as $path) {
            @unlink($path);
            @unlink($path.'.lock');
            @unlink($path.'.previous');
        }
        parent::tearDown();
    }

    public function test_public_pages_show_official_profile_content_without_technical_notes(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Mengembangkan potensi mahasiswa Teknik Informatika UCIC agar menjadi pribadi yang proaktif dan adaptif pada perkembangan zaman.')
            ->assertSee('Menjadikan HIMATIF pusat pengembangan potensi mahasiswa Teknik Informatika.')
            ->assertDontSee('Peran dalam Aplikasi');

        $this->get(route('profil.index'))
            ->assertOk()
            ->assertSee('Mengembangkan potensi mahasiswa Teknik Informatika UCIC agar menjadi pribadi yang proaktif dan adaptif pada perkembangan zaman.')
            ->assertSee('Menjadikan HIMATIF organisasi yang peduli akan kebutuhan mahasiswa Teknik Informatika.')
            ->assertSee('Menjadikan HIMATIF pusat pengembangan potensi mahasiswa Teknik Informatika.')
            ->assertSee('Membentuk karakter mahasiswa Teknik Informatika menjadi pribadi yang proaktif dan adaptif.')
            ->assertSee('Muhamad Marlo Putra Sagara')
            ->assertSee('Fiti Nabilah Zulfa')
            ->assertSee('Rizki Adha Sulaeman')
            ->assertSee('Bidang Professional')
            ->assertSee('Bidang Humanistik')
            ->assertSee('Bidang Hubungan Internal & Eksternal')
            ->assertDontSee('Nama pengurus belum dikonfirmasi');

        $this->get(route('kegiatan.index'))
            ->assertOk()
            ->assertSee('Rapat Kerja HIMATIF')
            ->assertSee('Deskripsi Singkat')
            ->assertSee('Detail')
            ->assertDontSee('Tambah Program Kerja')
            ->assertDontSee('Edit')
            ->assertDontSee('Hapus')
            ->assertDontSee('session browser')
            ->assertDontSee('tugas UTS');

        $this->get(route('kegiatan.show', '1'))->assertOk();
        $this->get(route('galeri.index'))->assertOk();
        $this->get(route('kontak.index'))->assertOk();
        $this->get('/buku')->assertNotFound();
    }

    public function test_guest_is_redirected_from_all_internal_areas(): void
    {
        $this->get(route('portal.dashboard'))->assertRedirectToRoute('login');
        $this->get(route('portal.rapat.index'))->assertRedirectToRoute('login');
        $this->get(route('portal.absensi.index'))->assertRedirectToRoute('login');
        $this->get(route('portal.absensi.create'))->assertRedirectToRoute('login');
        $this->get(route('pengurus.absensi.index'))->assertRedirectToRoute('login');
        $this->get(route('kegiatan.create'))->assertRedirectToRoute('login');
    }

    public function test_registration_creates_pending_account_with_password_hash(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Login Anggota HIMATIF');
        $this->get(route('register'))->assertOk()->assertSee('Konfirmasi password')->assertSee('name="_token"', false);
        $this->post(route('register.store'), $this->registrationData())
            ->assertRedirectToRoute('login')->assertSessionHas('success');

        $account = app(MemberAccountStore::class)->findByEmail('anggota@example.test');
        $this->assertNotNull($account);
        $this->assertSame('anggota', $account['role']);
        $this->assertSame('pending', $account['status_keanggotaan']);
        $this->assertNotSame('password-rahasia', $account['password_hash']);
        $this->assertTrue(Hash::check('password-rahasia', $account['password_hash']));
        $this->assertArrayNotHasKey('password', $account);
        $this->post(route('login.store'), ['email' => 'anggota@example.test', 'password' => 'password-rahasia'])->assertSessionHasErrors('email');
        $this->get(route('portal.dashboard'))->assertRedirectToRoute('login');
    }

    public function test_registration_rejects_duplicate_email_and_nim(): void
    {
        $this->post(route('register.store'), $this->registrationData())->assertRedirectToRoute('login');
        $this->from(route('register'))->post(route('register.store'), array_merge($this->registrationData(), ['nim' => '2310002']))->assertRedirectToRoute('register')->assertSessionHasErrors('email');
        $this->from(route('register'))->post(route('register.store'), array_merge($this->registrationData(), ['email' => 'lain@example.test']))->assertRedirectToRoute('register')->assertSessionHasErrors('nim');
    }

    public function test_invalid_password_is_rejected_and_verified_member_can_login(): void
    {
        $member = $this->createMember('2311001', 'verified');
        $this->from(route('login'))->post(route('login.store'), ['email' => $member['email'], 'password' => 'salah-password'])->assertRedirectToRoute('login')->assertSessionHasErrors('email');
        $this->post(route('login.store'), ['email' => $member['email'], 'password' => 'password-rahasia'])->assertRedirectToRoute('portal.dashboard');
        $this->get(route('portal.dashboard'))->assertOk()->assertSee('Selamat datang, Anggota 2311001')->assertSee('2311001');
    }

    public function test_member_cannot_open_pengurus_pages_or_manage_activities_and_meetings(): void
    {
        $member = $this->createMember('2311002', 'verified');
        $this->withSession(['himatif_account_id' => $member['id']]);
        $this->get(route('pengurus.absensi.index'))->assertForbidden();
        $this->get(route('pengurus.anggota.index'))->assertForbidden();
        $this->get(route('kegiatan.create'))->assertForbidden();
        $this->post(route('kegiatan.store'), $this->activityData())->assertForbidden();
        $this->patch(route('pengurus.rapat.status', 'R-002'))->assertForbidden();
        $this->post(route('pengurus.rapat.store'), [])->assertForbidden();
    }

    public function test_member_attendance_uses_logged_in_identity_and_rejects_closed_and_duplicate(): void
    {
        $member = $this->createMember('2311003', 'verified');
        $this->withSession(['himatif_account_id' => $member['id']]);

        $this->post(route('portal.absensi.store'), ['rapat_id' => 'R-001', 'nim' => '9999999'])
            ->assertRedirectToRoute('portal.absensi.index')->assertSessionHas('success');
        $records = app(AttendanceStore::class)->all();
        $this->assertCount(1, $records);
        $this->assertSame('2311003', $records[0]['nim']);
        $this->assertSame('R-001', $records[0]['rapat_id']);

        $this->from(route('portal.absensi.create'))->post(route('portal.absensi.store'), ['rapat_id' => 'R-001'])
            ->assertRedirectToRoute('portal.absensi.create')->assertSessionHas('error');
        $this->from(route('portal.absensi.create'))->post(route('portal.absensi.store'), ['rapat_id' => 'R-002'])
            ->assertRedirectToRoute('portal.absensi.create')->assertSessionHas('error');
        $this->assertCount(1, app(AttendanceStore::class)->all());
    }

    public function test_member_can_only_see_own_attendance(): void
    {
        $member = $this->createMember('2311004', 'verified');
        $other = $this->createMember('2311005', 'verified');
        $this->withSession(['himatif_account_id' => $other['id']]);
        $this->post(route('portal.absensi.store'), ['rapat_id' => 'R-001'])->assertRedirectToRoute('portal.absensi.index');
        $record = app(AttendanceStore::class)->all()[0];

        $this->withSession(['himatif_account_id' => $member['id']]);
        $this->get(route('portal.absensi.index'))->assertOk()->assertDontSee($other['nama'])->assertSee('Belum ada catatan kehadiran');
        $this->get(route('portal.absensi.show', $record['id']))->assertNotFound();
    }

    public function test_pengurus_can_verify_members_open_meetings_and_view_recap(): void
    {
        $admin = $this->createAdmin();
        $pending = app(MemberAccountStore::class)->registerAnggota([
            'nama' => 'Anggota Menunggu', 'nim' => '2311006', 'email' => 'menunggu@example.test', 'password_hash' => Hash::make('password-rahasia'),
        ])['account'];

        $this->withSession(['himatif_account_id' => $admin['id']]);
        $this->get(route('pengurus.anggota.index'))->assertOk()->assertSee($pending['email']);
        $this->patch(route('pengurus.anggota.approve', $pending['id']))->assertRedirectToRoute('pengurus.anggota.index')->assertSessionHas('success');
        $this->patch(route('pengurus.rapat.status', 'R-002'))->assertRedirectToRoute('portal.rapat.index');
        $this->get(route('portal.rapat.index'))->assertOk()->assertSee('Dibuka');
        $this->get(route('pengurus.rapat.create'))->assertOk()->assertSee('Tambah Jadwal Rapat');
        $this->post(route('pengurus.rapat.store'), ['judul' => 'Rapat Tambahan', 'tanggal' => '2027-01-10', 'lokasi' => 'Ruang A', 'deskripsi' => 'Pembahasan kegiatan.'])
            ->assertRedirectToRoute('portal.rapat.index');
        $newMeeting = collect(app(MeetingStore::class)->all())->firstWhere('judul', 'Rapat Tambahan');
        $this->assertNotNull($newMeeting);
        $this->assertSame('Ditutup', $newMeeting['status']);
        $this->get(route('pengurus.rapat.edit', $newMeeting['id']))->assertOk()->assertSee('Rapat Tambahan');
        $this->put(route('pengurus.rapat.update', $newMeeting['id']), ['judul' => 'Rapat Tambahan Revisi', 'tanggal' => '2027-01-12', 'lokasi' => 'Ruang B', 'deskripsi' => 'Agenda diperbarui.'])->assertRedirectToRoute('portal.rapat.index');
        $this->assertSame('Rapat Tambahan Revisi', app(MeetingStore::class)->find($newMeeting['id'])['judul']);
        $this->delete(route('pengurus.rapat.destroy', $newMeeting['id']))->assertRedirectToRoute('portal.rapat.index');
        $this->assertNull(app(MeetingStore::class)->find($newMeeting['id']));

        $this->withSession(['himatif_account_id' => $pending['id']]);
        $this->post(route('portal.absensi.store'), ['rapat_id' => 'R-002'])->assertRedirectToRoute('portal.absensi.index')->assertSessionHas('success');
        $this->withSession(['himatif_account_id' => $admin['id']]);
        $this->delete(route('pengurus.rapat.destroy', 'R-002'))->assertRedirectToRoute('portal.rapat.index')->assertSessionHas('error');
        $this->get(route('pengurus.absensi.index'))->assertOk()->assertSee('Anggota Menunggu')->assertSee('Evaluasi Program Kerja');
    }

    public function test_logout_invalidates_authentication_and_keeps_activity_session_data(): void
    {
        $admin = $this->createAdmin();
        $this->withSession(['himatif_account_id' => $admin['id'], 'daftarKegiatan' => app(HimatifSessionStore::class)->kegiatanContoh()]);
        $this->post(route('logout'))->assertRedirectToRoute('home')->assertSessionMissing('himatif_account_id')->assertSessionHas('daftarKegiatan');
        $this->get(route('portal.dashboard'))->assertRedirectToRoute('login');
        $this->get(route('kegiatan.index'))->assertOk()->assertSee('Rapat Kerja HIMATIF');
    }

    public function test_activity_routes_preserve_the_seven_uts_endpoints(): void
    {
        $this->assertSame(7, collect(app('router')->getRoutes())->filter(static fn ($route): bool => str_starts_with($route->uri(), 'kegiatan'))->count());
        $this->assertSame('kegiatan.index', app('router')->getRoutes()->getByName('kegiatan.index')->getName());
        $this->assertSame('kegiatan.destroy', app('router')->getRoutes()->getByName('kegiatan.destroy')->getName());
    }

    private function registrationData(array $overrides = []): array
    {
        return array_merge(['nama' => 'Anggota Contoh', 'nim' => '2311000', 'email' => 'anggota@example.test', 'password' => 'password-rahasia', 'password_confirmation' => 'password-rahasia'], $overrides);
    }

    private function createMember(string $nim, string $status): array
    {
        $result = app(MemberAccountStore::class)->registerAnggota([
            'nama' => 'Anggota '.$nim, 'nim' => $nim, 'email' => $nim.'@example.test', 'password_hash' => Hash::make('password-rahasia'),
        ]);
        $account = $result['account'];
        if ($status === 'verified') {
            app(MemberAccountStore::class)->approveAnggota($account['id']);
            $account = app(MemberAccountStore::class)->findById($account['id']);
        }

        return $account;
    }

    private function createAdmin(): array
    {
        return app(MemberAccountStore::class)->createInitialPengurus([
            'nama' => 'Pengurus Contoh', 'nim' => '2399001', 'email' => 'pengurus@example.test', 'password_hash' => Hash::make('password-rahasia'),
        ]);
    }

    private function activityData(): array
    {
        return ['kode' => 'KGT-998', 'nama' => 'Kegiatan Tes', 'kategori' => 'Tes', 'tanggal' => '2027-01-10', 'lokasi' => 'Ruang Kelas', 'status' => 'Akan datang', 'deskripsi' => 'Data tes'];
    }
}
