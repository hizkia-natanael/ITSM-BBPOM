<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Tickets\TicketDynamicPayloadCompiler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiptDeletionSubcategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_ticket_page_renders_sipt_hapus_sampel_and_hapus_sarana_forms(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Category::firstOrCreate(['slug' => 'sipt'], ['name' => 'SIPT']);

        $response = $this->actingAs($user)->get(route('tickets.create', ['service' => 'sipt']));

        $response->assertSuccessful()
            ->assertSee('Hapus Sampel')
            ->assertSee('Hapus Sarana')
            ->assertSee('No. Sampel')
            ->assertSee('No. ID Sarana')
            ->assertSee('Nama Sarana')
            ->assertSee('Konfirmasi Penghapusan Data Sampel SIPT')
            ->assertSee('Konfirmasi Penghapusan Data Sarana SIPT');
    }

    public function test_user_can_submit_sipt_hapus_sampel_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['slug' => 'sipt'], ['name' => 'SIPT']);

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'service_key' => 'sipt',
            'subcategory_key' => 'hapus_sampel',
            'category_id' => $category->id,
            'sipt_sample_number' => 'SMP-2026-DEL88',
            'sipt_commodity' => 'Pangan',
        ]);

        $ticket = Ticket::where('user_id', $user->id)
            ->where('service_key', 'sipt')
            ->where('subcategory', 'hapus_sampel')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertSame('Hapus Sampel', $ticket->subcategory_label);
        $this->assertStringContainsString('Hapus Sampel SMP-2026-DEL88 (Pangan)', $ticket->subject);
        $this->assertStringContainsString('**Nomor Sampel:** SMP-2026-DEL88', $ticket->description);
        $this->assertStringContainsString('**Komoditi:** Pangan', $ticket->description);
        $response->assertRedirect(route('tickets.show', $ticket));

        $this->actingAs($user)->get(route('tickets.show', $ticket))
            ->assertSuccessful()
            ->assertSee('Hapus Sampel')
            ->assertSee('SMP-2026-DEL88')
            ->assertSee('Pangan');
    }

    public function test_user_can_submit_sipt_hapus_sarana_ticket(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['slug' => 'sipt'], ['name' => 'SIPT']);

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'service_key' => 'sipt',
            'subcategory_key' => 'hapus_sarana',
            'category_id' => $category->id,
            'sipt_facility_id' => 'SRN-3374-00451',
            'sipt_facility_name' => 'Apotek Sehat Sentosa',
        ]);

        $ticket = Ticket::where('user_id', $user->id)
            ->where('service_key', 'sipt')
            ->where('subcategory', 'hapus_sarana')
            ->first();

        $this->assertNotNull($ticket);
        $this->assertSame('Hapus Sarana', $ticket->subcategory_label);
        $this->assertStringContainsString('Hapus Sarana Apotek Sehat Sentosa (ID: SRN-3374-00451)', $ticket->subject);
        $this->assertStringContainsString('**No. ID Sarana:** SRN-3374-00451', $ticket->description);
        $this->assertStringContainsString('**Nama Sarana:** Apotek Sehat Sentosa', $ticket->description);
        $response->assertRedirect(route('tickets.show', $ticket));

        $this->assertSame('ri-hashtag', TicketDynamicPayloadCompiler::getFieldIcon('No. ID Sarana'));
        $this->assertSame('ri-building-2-line', TicketDynamicPayloadCompiler::getFieldIcon('Nama Sarana'));
        $this->assertSame('ri-price-tag-3-line', TicketDynamicPayloadCompiler::getFieldIcon('Komoditi'));

        $this->actingAs($user)->get(route('tickets.show', $ticket))
            ->assertSuccessful()
            ->assertSee('Hapus Sarana')
            ->assertSee('SRN-3374-00451')
            ->assertSee('Apotek Sehat Sentosa');
    }
}
