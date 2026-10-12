<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_login_screen_can_be_rendered_without_emojis(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $content = $response->getContent();
        $hasEmoji = preg_match('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26BF}]/u', $content);
        $this->assertSame(0, $hasEmoji, 'Raw emojis found in /login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertSessionHas('welcome_toast', $user->name);
    }

    public function test_login_displays_welcome_toast_notification(): void
    {
        $user = User::factory()->create(['name' => 'Bambang Sudiro']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertSessionHas('welcome_toast', 'Bambang Sudiro');

        $dashboardResponse = $this->actingAs($user)
            ->withSession(['welcome_toast' => 'Bambang Sudiro'])
            ->get('/dashboard');

        $dashboardResponse->assertSuccessful();
        $dashboardResponse->assertSee('Selamat Datang, Bambang Sudiro!');
        $dashboardResponse->assertSee('Login Berhasil');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_admins_are_redirected_directly_to_admin_panel_upon_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->withSession(['url.intended' => '/tickets/create'])
            ->post('/login', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin');
    }

    public function test_users_are_redirected_directly_to_dashboard_even_with_intended_session(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->withSession(['url.intended' => '/admin'])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_authenticated_admin_visiting_login_is_redirected_to_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect('/admin');
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }
}
