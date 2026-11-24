<?php

namespace Tests\Feature;

use App\Services\MemberAccountStore;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class KegiatanCrudTest extends TestCase
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
        $admin = app(MemberAccountStore::class)->createInitialPengurus([
            'nama' => 'Pengurus Tes', 'nim' => '2399001', 'email' => 'pengurus@example.test',
            'password_hash' => Hash::make('password-aman'),
        ]);
        $this->withSession(['himatif_account_id' => $admin['id']]);
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

    public function test_activity_list_starts_with_five_session_examples_and_shows_required_columns(): void
    {
        $this->get(route('kegiatan.index'))
            ->assertOk()->assertSee('Rapat Kerja HIMATIF')->assertSee('Kode')->assertSee('Kategori')->assertSee('Deskripsi Singkat')->assertSee('KGT-001')->assertSee('Tambah Program Kerja')->assertSee('Edit')->assertSee('Hapus')->assertDontSee('session browser')
            ->assertSessionHas('daftarKegiatan', static fn (array $items): bool => count($items) === 5);
    }

    public function test_create_stores_a_valid_activity_and_redirects_with_a_message(): void
    {
        $this->post(route('kegiatan.store'), $this->inputKegiatan())
            ->assertRedirectToRoute('kegiatan.index')->assertSessionHas('success', 'Program kerja berhasil ditambahkan.')
            ->assertSessionHas('daftarKegiatan', static fn (array $items): bool => count($items) === 6
                && $items[5]['id'] === '6' && $items[5]['kode'] === 'KGT-006' && $items[5]['kategori'] === 'Pelatihan');
        $this->get(route('kegiatan.index'))->assertOk()->assertSee('Seminar Teknologi');
    }

    public function test_create_form_uses_csrf_named_route_and_validation(): void
    {
        $this->get(route('kegiatan.create'))->assertOk()->assertSee('name="_token"', false)->assertSee('action="'.route('kegiatan.store').'"', false);
        $this->from(route('kegiatan.create'))->post(route('kegiatan.store'), [])->assertRedirectToRoute('kegiatan.create')->assertSessionHasErrors(['kode', 'nama', 'kategori', 'tanggal', 'lokasi', 'status', 'deskripsi']);
    }

    public function test_code_must_be_unique_and_date_must_be_valid(): void
    {
        $this->from(route('kegiatan.create'))->post(route('kegiatan.store'), array_merge($this->inputKegiatan(), ['kode' => 'KGT-001']))->assertRedirectToRoute('kegiatan.create')->assertSessionHasErrors(['kode']);
        $this->from(route('kegiatan.create'))->post(route('kegiatan.store'), array_merge($this->inputKegiatan(), ['tanggal' => 'bukan tanggal']))->assertRedirectToRoute('kegiatan.create')->assertSessionHasErrors(['tanggal']);
    }

    public function test_detail_includes_category_and_localized_date(): void
    {
        $this->get(route('kegiatan.show', '1'))->assertOk()->assertSee('Rapat Kerja HIMATIF')->assertSee('Rapat')->assertSee('24 Oktober 2026')->assertSee('Ruang Kelas');
    }

    public function test_edit_form_has_old_values_csrf_and_put_method(): void
    {
        $this->get(route('kegiatan.edit', '1'))->assertOk()->assertSee('value="KGT-001"', false)->assertSee('name="_token"', false)->assertSee('name="_method" value="PUT"', false)->assertSee('action="'.route('kegiatan.update', '1').'"', false);
    }

    public function test_update_changes_the_activity_in_session(): void
    {
        $input = $this->inputKegiatan();
        $input['nama'] = 'Rapat Kerja Tahunan';
        $input['kategori'] = 'Rapat';
        $input['tanggal'] = '2027-02-12';
        $input['lokasi'] = 'Ruang Rapat';
        $input['status'] = 'Selesai';
        $input['deskripsi'] = 'Menyusun agenda kerja tahunan.';
        $this->withSession(['himatif_account_id' => app(MemberAccountStore::class)->findByEmail('pengurus@example.test')['id'], 'daftarKegiatan' => [$this->kegiatanContoh()], 'idKegiatanBerikutnya' => 2])
            ->put(route('kegiatan.update', '1'), $input)->assertRedirectToRoute('kegiatan.index')->assertSessionHas('success', 'Program kerja berhasil diperbarui.')
            ->assertSessionHas('daftarKegiatan', static fn (array $items): bool => $items[0]['nama'] === 'Rapat Kerja Tahunan' && $items[0]['kode'] === 'KGT-006' && $items[0]['kategori'] === 'Rapat');
    }

    public function test_delete_removes_last_activity_without_reseeding(): void
    {
        $this->withSession(['himatif_account_id' => app(MemberAccountStore::class)->findByEmail('pengurus@example.test')['id'], 'daftarKegiatan' => [$this->kegiatanContoh()], 'idKegiatanBerikutnya' => 2])
            ->delete(route('kegiatan.destroy', '1'))->assertRedirectToRoute('kegiatan.index')->assertSessionHas('success', 'Program kerja berhasil dihapus.')->assertSessionHas('daftarKegiatan', []);
        $this->get(route('kegiatan.index'))->assertOk()->assertSee('Belum ada program kerja yang ditampilkan.')->assertSessionHas('daftarKegiatan', []);
    }

    public function test_missing_activity_returns_404_for_show_edit_update_and_delete(): void
    {
        $this->get(route('kegiatan.show', '99'))->assertNotFound()->assertSeeText('Program kerja dengan ID 99 tidak ditemukan');
        $this->get(route('kegiatan.edit', '99'))->assertNotFound();
        $this->put(route('kegiatan.update', '99'), [])->assertNotFound();
        $this->delete(route('kegiatan.destroy', '99'))->assertNotFound();
    }

    public function test_delete_form_uses_csrf_delete_method_and_confirmation(): void
    {
        $this->get(route('kegiatan.index'))->assertOk()->assertSee('name="_token"', false)->assertSee('name="_method" value="DELETE"', false)->assertSee("confirm('Hapus program kerja ini?')", false);
    }

    public function test_user_supplied_html_is_escaped_in_activity_details(): void
    {
        $input = $this->inputKegiatan();
        $input['nama'] = '<script>alert(1)</script>';
        $input['deskripsi'] = '<img src=x onerror=alert(1)>';
        $this->post(route('kegiatan.store'), $input)->assertRedirectToRoute('kegiatan.index');
        $this->get(route('kegiatan.show', '6'))->assertOk()->assertSee($input['nama'])->assertDontSee($input['nama'], false)->assertDontSee($input['deskripsi'], false);
    }

    private function inputKegiatan(): array
    {
        return ['kode' => 'KGT-006', 'nama' => 'Seminar Teknologi', 'kategori' => 'Pelatihan', 'tanggal' => '2027-01-10', 'lokasi' => 'Aula Kampus', 'status' => 'Akan datang', 'deskripsi' => 'Seminar teknologi untuk anggota HIMATIF.'];
    }

    private function kegiatanContoh(): array
    {
        return ['id' => '1', 'kode' => 'KGT-001', 'nama' => 'Rapat Contoh', 'kategori' => 'Rapat', 'tanggal' => '2026-10-24', 'lokasi' => 'Ruang Kelas', 'status' => 'Akan datang', 'deskripsi' => 'Kegiatan contoh untuk pengujian.'];
    }
}
