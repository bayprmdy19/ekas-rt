<?php

namespace Tests\Feature;

use App\Models\KartuKeluarga;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KartuKeluargaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $warga;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Pengurus RT 01',
            'username' => 'admin_rt',
            'email' => 'admin@rt.id',
            'role' => 'admin',
        ]);

        $this->warga = User::factory()->create([
            'name' => 'Ahmad Warga',
            'username' => 'ahmad_warga',
            'email' => 'ahmad@warga.id',
            'role' => 'warga',
        ]);

        Warga::create([
            'nama_lengkap' => 'Ahmad Warga',
            'nik' => '3201000000000001',
            'nomor_rumah' => 'Blok C No. 01',
            'no_hp' => '081299998888',
            'status_tinggal' => 'tetap',
        ]);
    }

    public function test_admin_sidebar_contains_data_kk_above_data_warga(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Data KK');
        $response->assertSee(route('admin.kk.index'));
    }

    public function test_admin_can_view_data_kk_index(): void
    {
        KartuKeluarga::create([
            'nomor_kk' => '3201112233445566',
            'kepala_keluarga' => 'Ahmad Warga',
            'nik_kepala_keluarga' => '3201000000000001',
            'nomor_rumah' => 'Blok C No. 01',
            'jumlah_anggota' => 3,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kk.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Kartu Keluarga (KK)');
        $response->assertSee('Ahmad Warga');
        $response->assertSee('3201112233445566');
    }

    public function test_warga_cannot_access_admin_kk_index(): void
    {
        $response = $this->actingAs($this->warga)->get(route('admin.kk.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_view_kk_detail_json(): void
    {
        $kk = KartuKeluarga::create([
            'nomor_kk' => '3201112233445566',
            'kepala_keluarga' => 'Ahmad Warga',
            'nik_kepala_keluarga' => '3201000000000001',
            'nomor_rumah' => 'Blok C No. 01',
            'jumlah_anggota' => 3,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kk.show', $kk->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $kk->id,
            'kepala_keluarga' => 'Ahmad Warga',
            'nomor_kk' => '3201112233445566',
        ]);
    }

    public function test_admin_can_update_verification_status_of_kk(): void
    {
        $kk = KartuKeluarga::create([
            'nomor_kk' => '3201112233445566',
            'kepala_keluarga' => 'Ahmad Warga',
            'nik_kepala_keluarga' => '3201000000000001',
            'nomor_rumah' => 'Blok C No. 01',
            'jumlah_anggota' => 3,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.kk.update-status', $kk->id), [
            'status_verifikasi' => 'terverifikasi',
            'catatan_admin' => 'Dokumen sah.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kartu_keluargas', [
            'id' => $kk->id,
            'status_verifikasi' => 'terverifikasi',
            'catatan_admin' => 'Dokumen sah.',
        ]);
    }

    public function test_warga_can_view_kelengkapan_dokumen_page(): void
    {
        $response = $this->actingAs($this->warga)->get(route('warga.dokumen.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelengkapan Data Diri & Dokumen KK', false);
        $response->assertSee('Foto Kartu Keluarga (KK)', false);
        $response->assertSee('Foto KTP', false);
        $response->assertSee('Foto Kartu BPJS', false);
    }

    public function test_warga_can_upload_dokumen_kk_and_photos(): void
    {
        Storage::fake('public');

        $fileKK = UploadedFile::fake()->create('kk.jpg', 100, 'image/jpeg');
        $fileKTP = UploadedFile::fake()->create('ktp.jpg', 100, 'image/jpeg');
        $fileBPJS = UploadedFile::fake()->create('bpjs.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->warga)->post(route('warga.dokumen.store'), [
            'nomor_kk' => '3201998877665544',
            'nik_kepala_keluarga' => '3201000000000001',
            'nomor_rumah' => 'Blok C No. 01',
            'no_hp' => '081299998888',
            'jumlah_anggota' => 4,
            'foto_kk' => $fileKK,
            'foto_ktp' => $fileKTP,
            'foto_bpjs' => $fileBPJS,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kartu_keluargas', [
            'user_id' => $this->warga->id,
            'nomor_kk' => '3201998877665544',
            'kepala_keluarga' => 'Ahmad Warga',
            'status_verifikasi' => 'menunggu',
        ]);

        $kk = KartuKeluarga::where('user_id', $this->warga->id)->first();
        $this->assertNotNull($kk->foto_kk);
        $this->assertNotNull($kk->foto_ktp);
        $this->assertNotNull($kk->foto_bpjs);

        Storage::disk('public')->assertExists($kk->foto_kk);
        Storage::disk('public')->assertExists($kk->foto_ktp);
        Storage::disk('public')->assertExists($kk->foto_bpjs);
    }

    public function test_warga_navigation_contains_lengkapi_data_diri_above_logout(): void
    {
        $response = $this->actingAs($this->warga)->get(route('warga.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Profile', false);
        $response->assertSee('Lengkapi Data Diri & Dokumen KK');
        $response->assertSee('Log Out', false);
        $response->assertSee(route('warga.dokumen.index'));
    }

    public function test_profile_edit_page_contains_dokumen_kk_section(): void
    {
        $response = $this->actingAs($this->warga)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Kelengkapan Data Diri & Dokumen KK', false);
        $response->assertSee('Foto Kartu Keluarga (KK)', false);
    }
}
