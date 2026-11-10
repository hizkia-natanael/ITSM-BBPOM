<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardwareBmnBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tickets_create_renders_hardware_bmn_card_with_simpelbmn_link(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tickets.create'));

        $response->assertStatus(200);
        $response->assertSee('https://simpel.bbpomsemarang.id/', false);
        $response->assertSee('Hardware & BMN', false);
        $response->assertSee('SIMPEL BMN BBPOM', false);
        $response->assertSee('ri-hard-drive-3-line', false);
        $response->assertSee('ri-arrow-right-up-line', false);
    }

    public function test_hardware_bmn_card_component_has_no_raw_emojis(): void
    {
        $componentPath = resource_path('views/components/hardware-bmn-card.blade.php');
        $this->assertFileExists($componentPath);

        $content = file_get_contents($componentPath);
        $emojiPattern = '/[\x{1F300}-\x{1F9FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u';

        $this->assertSame(0, preg_match($emojiPattern, $content), 'Hardware BMN card component should not contain raw emojis.');
    }
}
