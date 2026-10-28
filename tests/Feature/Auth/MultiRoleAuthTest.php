<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiRoleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('Nomor HP');
        $response->assertSee('Contoh: Budi Santoso');
        $response->assertSee('Contoh: 081234567890');
        $response->assertDontSee('Nomor KK');
    }

    public function test_warga_can_register_with_nama_lengkap_and_no_hp(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'password' => 'Kombinasi123',
            'password_confirmation' => 'Kombinasi123',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'username' => '081234567890',
            'role' => 'warga',
            'is_profile_complete' => false,
        ]);
        $response->assertRedirect(route('warga.onboarding'));

        // Logout lalu test login warga yang belum onboarding akan diarahkan paksa ke onboarding
        auth()->logout();
        $loginWa = $this->post('/login', [
            'login' => '081234567890',
            'password' => 'Kombinasi123',
        ]);
        $this->assertAuthenticated();
        $loginWa->assertRedirect(route('warga.onboarding'));
    }

    public function test_warga_can_register_with_name_username_and_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Warga Baru',
            'username' => 'warga_baru',
            'password' => 'Kombinasi123',
            'password_confirmation' => 'Kombinasi123',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => 'warga_baru',
            'role' => 'warga',
        ]);
        $response->assertRedirect(route('warga.onboarding'));
    }

    public function test_registration_fails_if_password_lacks_numbers(): void
    {
        $response = $this->post('/register', [
            'name' => 'Warga Lemah',
            'username' => 'warga_lemah',
            'password' => 'hanyahurufaja',
            'password_confirmation' => 'hanyahurufaja',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_fails_if_password_under_8_chars(): void
    {
        $response = $this->post('/register', [
            'name' => 'Warga Pendek',
            'username' => 'warga_pendek',
            'password' => 'Huruf1',
            'password_confirmation' => 'Huruf1',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_admin_redirected_to_admin_dashboard_on_login(): void
    {
        $admin = User::factory()->create([
            'username' => 'ketua_rt',
            'email' => 'ketua@ekasrt.id',
            'password' => Hash::make('AdminKuat123'),
            'role' => 'admin',
        ]);

        // Login using username
        $response = $this->post('/login', [
            'login' => 'ketua_rt',
            'password' => 'AdminKuat123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_warga_redirected_to_warga_dashboard_on_login_using_email(): void
    {
        $warga = User::factory()->create([
            'username' => 'pak_joko',
            'email' => 'joko@warga.id',
            'password' => Hash::make('WargaKuat123'),
            'role' => 'warga',
        ]);

        // Login using email
        $response = $this->post('/login', [
            'login' => 'joko@warga.id',
            'password' => 'WargaKuat123',
        ]);

        $this->assertAuthenticatedAs($warga);
        $response->assertRedirect(route('warga.dashboard'));
    }

    public function test_warga_cannot_access_admin_dashboard(): void
    {
        $warga = User::factory()->create([
            'role' => 'warga',
        ]);

        $response = $this->actingAs($warga)->get('/admin/dashboard');
        $response->assertRedirect(route('warga.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_login_screen_has_forgot_password_link_and_password_toggle(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee(route('password.request'));
        $response->assertSee('togglePasswordVisibility');
    }

    public function test_public_dashboard_does_not_show_kas_and_iuran_tables(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('Status Iuran Warga');
        $response->assertDontSee('Transaksi Kas Terbaru');
    }

    public function test_admin_dashboard_shows_kas_and_iuran_tables(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Transaksi Kas Terbaru');
        $response->assertSee('Status Iuran Warga');
    }

    public function test_uncompleted_warga_is_redirected_to_onboarding_and_can_logout(): void
    {
        $warga = User::factory()->create([
            'role' => 'warga',
            'is_profile_complete' => false,
        ]);

        // Akses dashboard warga harus diredirect paksa ke onboarding
        $response = $this->actingAs($warga)->get('/warga/dashboard');
        $response->assertRedirect(route('warga.onboarding'));

        // Halaman onboarding dapat dibuka dan memuat tombol logout
        $onboardingView = $this->actingAs($warga)->get('/warga/onboarding');
        $onboardingView->assertStatus(200);
        $onboardingView->assertSee('Keluar / Logout');
        $onboardingView->assertSee(route('logout'));

        // Logout dapat dijalankan
        $logoutResponse = $this->actingAs($warga)->post('/logout');
        $this->assertGuest();
        $logoutResponse->assertRedirect('/');
    }

    public function test_warga_can_complete_onboarding_and_login_with_no_kk(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $user = User::factory()->create([
            'name' => 'Ahmad Dahlan',
            'username' => '089876543210',
            'no_hp' => '089876543210',
            'password' => Hash::make('Password123'),
            'role' => 'warga',
            'is_profile_complete' => false,
        ]);

        $fileKtp = \Illuminate\Http\UploadedFile::fake()->create('ktp.jpg', 500, 'image/jpeg');
        $fileKk = \Illuminate\Http\UploadedFile::fake()->create('kk.pdf', 800, 'application/pdf');

        $payload = [
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '1985-08-17',
            'status_domisili' => 'tetap',
            'nomor_rumah' => 'Blok C1 No. 5',
            'is_berkeluarga' => '1',
            'nik' => '3301234567890001',
            'no_kk' => '3301234567890002',
            'file_ktp' => $fileKtp,
            'file_kk' => $fileKk,
            'memiliki_anak' => '1',
            'anak' => [
                [
                    'nama' => 'Bintang Pratama',
                    'umur' => '6',
                    'tempat_lahir' => 'Semarang',
                    'tanggal_lahir' => '2020-01-10',
                ],
            ],
        ];

        $postResponse = $this->actingAs($user)->post('/warga/onboarding', $payload);
        $postResponse->assertRedirect(route('warga.dashboard'));

        // Cek bahwa user telah terupdate is_profile_complete = true dan no_kk tersimpan
        $user->refresh();
        $this->assertTrue($user->is_profile_complete);
        $this->assertEquals('3301234567890002', $user->no_kk);

        // Cek data anak tersimpan via relasi
        $wargaProfile = \App\Models\Warga::where('user_id', $user->id)->first();
        $this->assertNotNull($wargaProfile);
        $this->assertCount(1, $wargaProfile->anaks);
        $this->assertEquals('Bintang Pratama', $wargaProfile->anaks->first()->nama_anak);

        // Logout lalu login kembali menggunakan Nomor KK
        auth()->logout();
        $loginWithKk = $this->post('/login', [
            'login' => '3301234567890002',
            'password' => 'Password123',
        ]);
        $this->assertAuthenticated();
        $loginWithKk->assertRedirect(route('warga.dashboard'));
    }
}
