<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\UnhandledTicketsAlertWidget;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KetuaTimMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_ketua_tim_role_has_read_only_access_and_can_remind_it_team(): void
    {
        $ketua = User::factory()->create(['name' => 'Bapak Ketua Tim', 'role' => 'ketua_tim']);
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::firstOrCreate(['slug' => 'aplikasi'], ['name' => 'Aplikasi']);

        $ticket = Ticket::create([
            'ticket_number' => 'TIK-ELITE-002',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Kendala Server Database',
            'description' => 'Server tidak merespons',
            'status' => 'pending',
        ]);

        $this->assertTrue($ketua->isKetuaTim());
        $this->assertFalse($ketua->isAdmin());

        // Policy checks: Ketua Tim cannot create or edit categories/users
        $this->assertFalse(CategoryResource::canCreate());
        $this->assertFalse(CategoryResource::canEdit($category));
        $this->assertFalse(CategoryResource::canDelete($category));
        $this->assertFalse(UserResource::canCreate());
        $this->assertFalse(UserResource::canEdit($user));
        $this->assertFalse(UserResource::canDelete($user));

        // Ketua Tim can access Filament Admin Panel with "Ketua Tim" brand label and without Mode Pemantauan badge
        $this->actingAs($ketua)->get('/admin')
            ->assertOk()
            ->assertSee('Ketua Tim')
            ->assertDontSee('Mode Pemantauan Ketua Tim')
            ->assertSee('Aduan Belum Ditangani');

        // Ketua Tim does not see any create buttons on category or user list routes
        $this->actingAs($ketua)->get('/admin/categories')
            ->assertOk()
            ->assertDontSee('Tambah Kategori Layanan');

        $this->actingAs($ketua)->get('/admin/users')
            ->assertOk()
            ->assertDontSee('Tambah Pegawai');

        // Ketua Tim is blocked from creating tickets and does not see create buttons on user routes
        $this->actingAs($ketua)->get('/tickets/create')
            ->assertForbidden();

        $this->actingAs($ketua)->get('/tickets')
            ->assertOk()
            ->assertDontSee('Buat Pengaduan Baru');

        // Ketua Tim cannot mutate or save ticket (blocked with 403)
        Livewire::actingAs($ketua)
            ->test(EditTicket::class, ['record' => $ticket->getKey()])
            ->fillForm(['status' => 'diproses'])
            ->call('save')
            ->assertForbidden();

        // Ketua Tim can trigger remindItTeam action on unhandled tickets widget
        Livewire::actingAs($ketua)
            ->test(UnhandledTicketsAlertWidget::class)
            ->assertSee('Ingatkan Tim TI')
            ->call('remindItTeam', $ticket->id)
            ->assertNotified('Pengingat Terkirim ke Tim TI');

        // Ketua Tim is blocked from claiming tickets
        Livewire::actingAs($ketua)
            ->test(UnhandledTicketsAlertWidget::class)
            ->call('claimTicket', $ticket->id)
            ->assertNotified('Akses Monitoring');

        $ticket->refresh();
        $this->assertNull($ticket->handled_by_id);
    }
}
