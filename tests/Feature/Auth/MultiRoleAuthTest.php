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
        $response->assertSee('Username');
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
        $response->assertRedirect(route('warga.dashboard'));
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
}
