<?php

namespace Tests\Feature;

use App\Models\Administrasi;
use App\Models\Pengaduan;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSuratTest extends TestCase
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

    public function test_admin_can_view_surat_index_page_with_new_action_buttons(): void
    {
        $surat = Administrasi::create([
            'user_id' => $this->wargaUser->id,
            'nama_pengaju' => 'Budi Santoso',
            'nomor_rumah' => 'Blok B No. 04',
            'jenis_surat' => 'Surat Pengantar Nikah',
            'keperluan' => 'Pendaftaran pernikahan di KUA',
            'status' => 'menunggu',
            'nomor_surat' => '001/NIK/RT/10/2026',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.surat.index'));

        $response->assertStatus(200);
        $response->assertSee('Surat', false);
        $response->assertSee('Budi Santoso', false);
        $response->assertSee('Surat Pengantar Nikah', false);
        // Verify action buttons exist in view
        $response->assertSee('openDetailSuratModal', false);
        $response->assertSee('openKirimModal', false);
        $response->assertSee('Pratinjau & Isi Dokumen Surat', false);
        $response->assertSee('Kirim Surat ke Pemohon', false);
    }

    public function test_admin_can_send_surat_to_pemohon(): void
    {
        Warga::create([
            'nama_lengkap' => 'Budi Santoso',
            'nik' => '3201010101010001',
            'no_kk' => '3201010101010000',
            'status_keluarga' => 'Kepala Keluarga',
            'nomor_rumah' => 'Blok B No. 04',
            'blok' => 'Blok B',
            'no_hp' => '081298765432',
            'status_tinggal' => 'tetap',
        ]);

        $surat = Administrasi::create([
            'user_id' => $this->wargaUser->id,
            'nama_pengaju' => 'Budi Santoso',
            'nomor_rumah' => 'Blok B No. 04',
            'jenis_surat' => 'Surat Pengantar Nikah',
            'keperluan' => 'Pendaftaran pernikahan di KUA',
            'status' => 'menunggu',
            'nomor_surat' => '001/NIK/RT/10/2026',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.surat.kirim', $surat->id), [
            'no_hp' => '081298765432',
            'pesan' => 'Halo Bpk Budi, surat nikah Anda telah disetujui.',
            'catatan' => 'Terkirim ke WhatsApp pemohon.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'surat' => [
                'id' => $surat->id,
                'status' => 'disetujui',
                'status_label' => 'Disetujui',
            ],
        ]);

        $surat->refresh();
        $this->assertEquals('disetujui', $surat->status);
        $this->assertEquals('Terkirim ke WhatsApp pemohon.', $surat->catatan);

        // Verify synced into Pengaduan table so citizen portal receives 'selesai' status
        $this->assertDatabaseHas('pengaduans', [
            'nama_pelapor' => 'Budi Santoso',
            'judul_laporan' => 'Surat Pengantar Nikah',
            'status' => 'selesai',
        ]);
    }

    public function test_admin_can_view_cetak_surat(): void
    {
        $surat = Administrasi::create([
            'user_id' => $this->wargaUser->id,
            'nama_pengaju' => 'Siti Nurhaliza',
            'nomor_rumah' => 'Blok C No. 12',
            'jenis_surat' => 'Surat Keterangan Usaha (SKU)',
            'keperluan' => 'Pengajuan KUR BRI',
            'status' => 'disetujui',
            'nomor_surat' => '005/SKU/RT/10/2026',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.surat.cetak', $surat->id));

        $response->assertStatus(200);
        $response->assertSee('Siti Nurhaliza', false);
        $response->assertSee('005/SKU/RT/10/2026', false);
        $response->assertSee('SURAT PENGANTAR RT', false);
    }

    public function test_non_admin_cannot_send_or_view_admin_surat(): void
    {
        $surat = Administrasi::create([
            'user_id' => $this->wargaUser->id,
            'nama_pengaju' => 'Warga Biasa',
            'nomor_rumah' => 'Blok A No. 01',
            'jenis_surat' => 'Surat Pengantar RT',
            'keperluan' => 'Administrasi',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->wargaUser)->get(route('admin.surat.index'));
        $response->assertRedirect(route('warga.dashboard'));

        $postResponse = $this->actingAs($this->wargaUser)->postJson(route('admin.surat.kirim', $surat->id), []);
        $postResponse->assertStatus(403);
    }
}
