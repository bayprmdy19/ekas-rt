<?php

namespace Tests\Feature;

use App\Models\Pengaduan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPengaduanTest extends TestCase
{
    use RefreshDatabase;

    protected function getAdminUser(): User
    {
        return User::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'name' => 'Admin Penguji',
                'email' => 'admin_test@ekasrt.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
    }

    public function test_admin_can_view_pengaduan_index()
    {
        $admin = $this->getAdminUser();

        Pengaduan::create([
            'nomor_rumah' => 'Blok U No. 11',
            'nama_pelapor' => 'Arif',
            'kategori' => 'Lampu Jalan',
            'judul_laporan' => 'Lampu Jalan Blok U11/12 Mati',
            'isi_laporan' => 'Lampu penerangan mati total',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pengaduan.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaduan Warga');
        $response->assertViewHas('pengaduans');
    }

    public function test_update_status_to_selesai_sets_selesai_at_automatically()
    {
        $admin = $this->getAdminUser();

        $pengaduan = Pengaduan::create([
            'nomor_rumah' => 'Blok T No. 05',
            'nama_pelapor' => 'Warga Uji',
            'kategori' => 'Lampu Jalan',
            'judul_laporan' => 'Aduan Uji Coba Selesai',
            'isi_laporan' => 'Isi laporan uji coba',
            'status' => 'diproses',
            'selesai_at' => null,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.pengaduan.update-status', $pengaduan->id), [
            'status' => 'selesai',
        ]);

        $response->assertRedirect(route('admin.pengaduan.index'));

        $pengaduan->refresh();
        $this->assertEquals('selesai', $pengaduan->status);
        $this->assertNotNull($pengaduan->selesai_at);
        $this->assertTrue($pengaduan->selesai_at->isToday());

        $pengaduan->delete();
    }

    public function test_auto_rate_sets_rating_5_after_24_hours()
    {
        $expiredCompleted = Pengaduan::create([
            'nomor_rumah' => 'Blok E No. 01',
            'nama_pelapor' => 'Warga Otomasi',
            'kategori' => 'Sampah',
            'judul_laporan' => 'Aduan Uji Otomasi 24 Jam',
            'isi_laporan' => 'Uji coba otomatisasi rating',
            'status' => 'selesai',
            'selesai_at' => Carbon::now()->subHours(26),
            'rating' => null,
            'ulasan_warga' => null,
        ]);

        // Jalankan fungsi otomatisasi
        $affected = Pengaduan::autoResolveExpiredRatings();

        $this->assertGreaterThanOrEqual(1, $affected);

        $expiredCompleted->refresh();
        $this->assertEquals(5, $expiredCompleted->rating);
        $this->assertEquals('Otomatis selesai oleh sistem (Layanan tuntas)', $expiredCompleted->ulasan_warga);

        $expiredCompleted->delete();
    }

    public function test_auto_rate_does_not_override_if_within_24_hours()
    {
        $recentCompleted = Pengaduan::create([
            'nomor_rumah' => 'Blok E No. 02',
            'nama_pelapor' => 'Warga Baru Selesai',
            'kategori' => 'Fasilitas',
            'judul_laporan' => 'Aduan Baru Selesai 2 Jam Lalu',
            'isi_laporan' => 'Belum 24 jam',
            'status' => 'selesai',
            'selesai_at' => Carbon::now()->subHours(2),
            'rating' => null,
            'ulasan_warga' => null,
        ]);

        Pengaduan::autoResolveExpiredRatings();

        $recentCompleted->refresh();
        $this->assertNull($recentCompleted->rating);
        $this->assertTrue($recentCompleted->isMenungguRatingWarga());

        $recentCompleted->delete();
    }

    public function test_admin_can_delete_pengaduan()
    {
        $admin = $this->getAdminUser();

        $pengaduan = Pengaduan::create([
            'nomor_rumah' => 'Blok H No. 03',
            'nama_pelapor' => 'Warga Dihapus',
            'kategori' => 'Saluran Air',
            'judul_laporan' => 'Aduan Akan Dihapus',
            'isi_laporan' => 'Akan segera dihapus',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.pengaduan.destroy', $pengaduan->id));

        $response->assertRedirect(route('admin.pengaduan.index'));
        $this->assertDatabaseMissing('pengaduans', ['id' => $pengaduan->id]);
    }

    public function test_warga_can_view_and_submit_rating_for_completed_pengaduan()
    {
        $wargaUser = User::firstOrCreate(
            ['username' => 'warga_rating_test'],
            [
                'name' => 'Warga Penilai',
                'email' => 'warga_penilai@ekasrt.id',
                'password' => bcrypt('password'),
                'role' => 'warga',
            ]
        );

        $pengaduan = Pengaduan::create([
            'nomor_rumah' => 'Blok K No. 07',
            'nama_pelapor' => 'Warga Penilai',
            'kategori' => 'Sampah',
            'judul_laporan' => 'Pengangkutan Sampah Rutin',
            'isi_laporan' => 'Sampah sudah diangkut bersih',
            'status' => 'selesai',
            'selesai_at' => Carbon::now()->subHours(2),
            'rating' => null,
            'ulasan_warga' => null,
        ]);

        // 1. Cek warga bisa melihat di dashboard
        $responseDashboard = $this->actingAs($wargaUser)->get(route('warga.dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('Evaluasi Pelayanan');

        // 2. Warga submit rating
        $responseRating = $this->actingAs($wargaUser)->patch(route('warga.pengaduan.rating', $pengaduan->id), [
            'rating' => 5,
            'ulasan_warga' => 'Terima kasih, pelayanan cepat dan lingkungan bersih kembali!',
        ]);

        $responseRating->assertRedirect();
        $responseRating->assertSessionHas('success');

        $pengaduan->refresh();
        $this->assertEquals(5, $pengaduan->rating);
        $this->assertEquals('Terima kasih, pelayanan cepat dan lingkungan bersih kembali!', $pengaduan->ulasan_warga);
    }
}
