<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuideBookTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'user',
            'nip' => '199001012020011001',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'nip' => '198501012015011002',
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_guide_book(): void
    {
        $response = $this->get('/panduan');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_guide_book_page(): void
    {
        $response = $this->actingAs($this->user)->get('/panduan');

        $response->assertOk();
        $response->assertSee('Buku Panduan Resmi ITSM');
        $response->assertSee('Urgensi Digitalisasi Layanan TI');
        $response->assertSee('Fleksibilitas Layanan');
        $response->assertSee('Transparansi Penuh');
        $response->assertSee('12 Katalog Layanan TI Terpadu');
        $response->assertSee('Detail Alur Kerja Aplikasi ITSM');
        $response->assertSee('Tim TIK BBPOM Semarang');
        $response->assertSee('Tanya Jawab Umum (FAQ)');
        $response->assertSee('Tim TIK BBPOM Di semarang');

        // Ensure removed items per user revision are NOT present
        $response->assertDontSee('SOP-TI-BBPOM-2026');
        $response->assertDontSee('Standar SLA & Waktu Penyelesaian');
        $response->assertDontSee('LEMBAR PENGESAHAN');
        $response->assertDontSee('Landasan Hukum & Kebijakan');
    }

    public function test_admin_can_access_guide_book_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/panduan');

        $response->assertOk();
        $response->assertSee('Buku Panduan Resmi ITSM');
    }

    public function test_guide_book_route_alias_redirects_to_panduan(): void
    {
        $response = $this->actingAs($this->user)->get('/guide-book');
        $response->assertRedirect('/panduan');
    }

    public function test_guide_book_does_not_contain_raw_emojis(): void
    {
        $response = $this->actingAs($this->user)->get('/panduan');
        $content = $response->getContent();

        // Check common emoji code ranges to ensure zero raw emoji compliance
        $emojiPattern = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';

        $this->assertDoesNotMatchRegularExpression($emojiPattern, $content, 'Guide book view contains raw emojis which violate AGENTS.md rules.');
    }
}
