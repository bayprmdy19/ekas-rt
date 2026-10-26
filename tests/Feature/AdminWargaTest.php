<?php

namespace Tests\Feature;

use App\Models\KartuKeluarga;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWargaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $wargaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Pengurus RT 01',
            'username' => 'admin_rt',
            'email' => 'admin@rt.id',
            'role' => 'admin',
        ]);

        $this->wargaUser = User::factory()->create([
            'name' => 'Warga Biasa',
            'username' => 'warga_biasa',
            'email' => 'warga@rt.id',
            'role' => 'warga',
        ]);
    }

    public function test_admin_can_view_warga_index_page(): void
    {
        Warga::create([
            'nama_lengkap' => 'Bambang Sudiro',
            'nik' => '3201888888880001',
            'no_kk' => '3201888888880000',
            'status_keluarga' => 'Kepala Keluarga',
            'nomor_rumah' => 'Blok A No. 05',
            'blok' => 'Blok A',
            'no_hp' => '081233334444',
            'status_tinggal' => 'tetap',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.warga.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Warga', false);
        $response->assertSee('KELOLA DATA KEPENDUDUKAN DAN REPOSITORI BERKAS KELUARGA', false);
        $response->assertSee('Tambah Warga', false);
        $response->assertSee('SEARCH', false);
        $response->assertSee('BLOK / GANG', false);
        $response->assertSee('STATUS KELUARGA', false);
        $response->assertSee('STATUS DOMISILI', false);
        $response->assertSee('Bambang Sudiro', false);
        $response->assertSee('Kepala Keluarga', false);
        $response->assertSee('3201888888880001', false);
        $response->assertSee('Edit', false);
        $response->assertSee('Hapus', false);
        $response->assertSee('Berkas', false);
    }

    public function test_warga_cannot_access_admin_warga_index(): void
    {
        $response = $this->actingAs($this->wargaUser)->get(route('admin.warga.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_filter_warga_by_search_keyword(): void
    {
        Warga::create([
            'nama_lengkap' => 'Joko Anwar',
            'nik' => '3201111122223333',
            'no_kk' => '3201111122220000',
            'status_keluarga' => 'Kepala Keluarga',
            'nomor_rumah' => 'Blok B No. 10',
            'blok' => 'Blok B',
            'no_hp' => '081211112222',
            'status_tinggal' => 'tetap',
        ]);

        Warga::create([
            'nama_lengkap' => 'Susanti Megawati',
            'nik' => '3201444455556666',
            'no_kk' => '3201444455550000',
            'status_keluarga' => 'Istri',
            'nomor_rumah' => 'Blok C No. 03',
            'blok' => 'Blok C',
            'no_hp' => '081244445555',
            'status_tinggal' => 'kontrak',
        ]);

        // Search Joko
        $response = $this->actingAs($this->admin)->get(route('admin.warga.index', ['q' => 'Joko']));
        $response->assertStatus(200);
        $response->assertSee('Joko Anwar', false);
        $response->assertDontSee('Susanti Megawati', false);
    }

    public function test_filter_warga_by_blok(): void
    {
        Warga::create([
            'nama_lengkap' => 'Warga Blok A',
            'nik' => '3201000000000010',
            'nomor_rumah' => 'Blok A No. 01',
            'blok' => 'Blok A',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'tetap',
        ]);

        Warga::create([
            'nama_lengkap' => 'Warga Blok B',
            'nik' => '3201000000000020',
            'nomor_rumah' => 'Blok B No. 02',
            'blok' => 'Blok B',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'tetap',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.warga.index', ['blok' => 'Blok A']));
        $response->assertStatus(200);
        $response->assertSee('Warga Blok A', false);
        $response->assertDontSee('Warga Blok B', false);
    }

    public function test_filter_warga_by_status_keluarga_and_domisili(): void
    {
        Warga::create([
            'nama_lengkap' => 'Istri Teladan',
            'nik' => '3201000000000030',
            'nomor_rumah' => 'Blok A No. 01',
            'blok' => 'Blok A',
            'status_keluarga' => 'Istri',
            'status_tinggal' => 'tetap',
        ]);

        Warga::create([
            'nama_lengkap' => 'Anak Kontrak',
            'nik' => '3201000000000040',
            'nomor_rumah' => 'Blok D No. 05',
            'blok' => 'Blok D',
            'status_keluarga' => 'Anak',
            'status_tinggal' => 'kontrak',
        ]);

        // Filter Istri
        $responseIstri = $this->actingAs($this->admin)->get(route('admin.warga.index', ['status_keluarga' => 'Istri']));
        $responseIstri->assertSee('Istri Teladan', false);
        $responseIstri->assertDontSee('Anak Kontrak', false);

        // Filter Kontrak
        $responseKontrak = $this->actingAs($this->admin)->get(route('admin.warga.index', ['status_domisili' => 'Kontrak/Kos']));
        $responseKontrak->assertSee('Anak Kontrak', false);
        $responseKontrak->assertDontSee('Istri Teladan', false);
    }

    public function test_admin_can_store_new_warga(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.warga.store'), [
            'nama_lengkap' => 'Gunawan Wibisono',
            'nik' => '3201777788889999',
            'no_kk' => '3201777788880000',
            'status_keluarga' => 'Kepala Keluarga',
            'nomor_rumah' => 'Blok E No. 09',
            'blok' => 'Blok E',
            'no_hp' => '081277778888',
            'status_tinggal' => 'tetap',
        ]);

        $response->assertRedirect(route('admin.warga.index'));
        $this->assertDatabaseHas('wargas', [
            'nama_lengkap' => 'Gunawan Wibisono',
            'nik' => '3201777788889999',
            'status_keluarga' => 'Kepala Keluarga',
            'blok' => 'Blok E',
        ]);
    }

    public function test_admin_can_update_warga(): void
    {
        $warga = Warga::create([
            'nama_lengkap' => 'Nama Lama',
            'nik' => '3201000011112222',
            'nomor_rumah' => 'Blok A No. 01',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'tetap',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.warga.update', $warga->id), [
            'nama_lengkap' => 'Nama Baru Diedit',
            'nik' => '3201000011112222',
            'no_kk' => '3201999900001111',
            'status_keluarga' => 'Istri',
            'nomor_rumah' => 'Blok B No. 02',
            'blok' => 'Blok B',
            'no_hp' => '081234567899',
            'status_tinggal' => 'kontrak',
        ]);

        $response->assertRedirect(route('admin.warga.index'));
        $this->assertDatabaseHas('wargas', [
            'id' => $warga->id,
            'nama_lengkap' => 'Nama Baru Diedit',
            'status_keluarga' => 'Istri',
            'status_tinggal' => 'kontrak',
        ]);
    }

    public function test_admin_can_delete_warga(): void
    {
        $warga = Warga::create([
            'nama_lengkap' => 'Warga Dihapus',
            'nik' => '3201999999999999',
            'nomor_rumah' => 'Blok A No. 99',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'tetap',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.warga.destroy', $warga->id));

        $response->assertRedirect(route('admin.warga.index'));
        $this->assertDatabaseMissing('wargas', [
            'id' => $warga->id,
        ]);
    }

    public function test_admin_can_fetch_berkas_json(): void
    {
        $kk = KartuKeluarga::create([
            'nomor_kk' => '3201888800001111',
            'kepala_keluarga' => 'Kepala Rumah Tangga',
            'nomor_rumah' => 'Blok A No. 15',
            'foto_kk' => 'dokumen_kk/sample_kk.svg',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $warga = Warga::create([
            'nama_lengkap' => 'Kepala Rumah Tangga',
            'nik' => '3201888800002222',
            'no_kk' => '3201888800001111',
            'nomor_rumah' => 'Blok A No. 15',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'tetap',
            'kartu_keluarga_id' => $kk->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.warga.berkas', $warga->id));

        $response->assertStatus(200);
        $response->assertJson([
            'warga' => [
                'nama' => 'Kepala Rumah Tangga',
                'status_keluarga' => 'Kepala Keluarga',
            ],
            'kk' => [
                'nomor_kk' => '3201888800001111',
                'status_verifikasi' => 'terverifikasi',
            ],
        ]);
    }

    public function test_admin_can_upload_berkas(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $warga = Warga::create([
            'nama_lengkap' => 'Warga Unggah Berkas',
            'nik' => '3201999900001234',
            'no_kk' => '3201999900005678',
            'nomor_rumah' => 'Blok B No. 08',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'tetap',
        ]);

        $fileKk = \Illuminate\Http\UploadedFile::fake()->create('scan_kk.jpg', 150, 'image/jpeg');
        $fileKtp = \Illuminate\Http\UploadedFile::fake()->create('ktp.jpg', 150, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('admin.warga.upload-berkas', $warga->id), [
            'foto_kk' => $fileKk,
            'foto_ktp' => $fileKtp,
        ]);

        $response->assertRedirect(route('admin.warga.index'));
        $response->assertSessionHas('success');

        $warga->refresh();
        $this->assertNotNull($warga->kartu_keluarga_id);

        $kk = KartuKeluarga::find($warga->kartu_keluarga_id);
        $this->assertNotNull($kk);
        $this->assertNotNull($kk->foto_kk);
        $this->assertNotNull($kk->foto_ktp);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($kk->foto_kk);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($kk->foto_ktp);
    }
}
