<?php

namespace Tests\Feature;

use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Tickets\Pages\ListTickets;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Penguji TI',
            'email' => 'admin_export_' . uniqid() . '@bbpom.go.id',
            'role' => 'admin',
        ]);

        $this->regularUser = User::factory()->create([
            'name' => 'Pegawai Pelapor',
            'email' => 'user_export_' . uniqid() . '@bbpom.go.id',
            'role' => 'user',
        ]);

        // Seed 12 official categories
        $categories = [
            1 => ['name' => 'SIPT', 'slug' => 'sipt'],
            2 => ['name' => 'Srikandi', 'slug' => 'srikandi'],
            3 => ['name' => 'Email Corporate', 'slug' => 'email-corporate'],
            4 => ['name' => 'Sertifikat Elektronik (BSrE)', 'slug' => 'sertifikat-bsre'],
            5 => ['name' => 'Fasilitas Zoom', 'slug' => 'zoom'],
            6 => ['name' => 'Formulir & Kegiatan', 'slug' => 'formulir-kegiatan'],
            7 => ['name' => 'Internet & Jaringan', 'slug' => 'internet'],
            8 => ['name' => 'Pengelolaan Subsite', 'slug' => 'subsite'],
            9 => ['name' => 'Desain Infografis', 'slug' => 'infografis'],
            10 => ['name' => 'Install Ulang Windows', 'slug' => 'install-windows'],
            11 => ['name' => 'Lisensi Microsoft Office', 'slug' => 'lisensi-office'],
            12 => ['name' => 'Layanan TIK Lainnya', 'slug' => 'lainnya'],
        ];

        foreach ($categories as $id => $data) {
            Category::firstOrCreate(['id' => $id], ['name' => $data['name'], 'slug' => $data['slug']]);
        }
    }

    public function test_guest_cannot_access_export_endpoint(): void
    {
        $response = $this->get(route('admin.tickets.export'));
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_export_endpoint(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.tickets.export'));
        $response->assertForbidden();
    }

    public function test_admin_can_export_all_tickets_to_pdf(): void
    {
        Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'srikandi',
            'category_id' => 2,
            'subject' => 'Kendala Registrasi Naskah Dinas',
            'description' => 'Tidak dapat melakukan penomoran surat pada aplikasi Srikandi.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'pdf',
            'category' => 'all',
            'period_type' => 'all',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_export_all_tickets_to_excel(): void
    {
        Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'sipt',
            'category_id' => 1,
            'subject' => 'Gagal Input Data Pengujian Sampel',
            'description' => 'Terjadi error internal saat validasi laboratorium.',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'excel',
            'category' => 'all',
            'period_type' => 'all',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_export_each_of_the_12_categories(): void
    {
        $all12Keys = [
            'sipt', 'srikandi', 'email_corporate', 'sertifikat_bse',
            'zoom', 'formulir_kegiatan', 'internet', 'subsite',
            'infografis', 'install_windows', 'lisensi_office', 'lainnya'
        ];

        foreach ($all12Keys as $idx => $key) {
            Ticket::create([
                'user_id' => $this->regularUser->id,
                'service_key' => $key,
                'category_id' => $idx + 1,
                'subject' => "Pengujian Export Layanan {$key}",
                'description' => "Deskripsi pengaduan untuk pengujian kategori {$key}",
                'status' => 'selesai',
            ]);
        }

        // Test export for each of the 12 options
        foreach ($all12Keys as $key) {
            $pdfResponse = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
                'format' => 'pdf',
                'category' => $key,
                'period_type' => 'all',
            ]));
            $pdfResponse->assertOk();

            $excelResponse = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
                'format' => 'excel',
                'category' => $key,
                'period_type' => 'all',
            ]));
            $excelResponse->assertOk();
        }
    }

    public function test_admin_can_export_by_month_and_year(): void
    {
        // Ticket in October 2026
        $ticketOct = Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'zoom',
            'category_id' => 5,
            'subject' => 'Permintaan Link Zoom Meeting',
            'description' => 'Rapat koordinasi teknis dinas.',
            'status' => 'selesai',
        ]);
        $ticketOct->created_at = '2026-10-02 10:00:00';
        $ticketOct->save();

        // Ticket in January 2025
        $ticketJan = Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'zoom',
            'category_id' => 5,
            'subject' => 'Zoom Tahun Lalu',
            'description' => 'Rapat tahun lalu.',
            'status' => 'selesai',
        ]);
        $ticketJan->created_at = '2025-01-15 10:00:00';
        $ticketJan->save();

        $response = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'pdf',
            'category' => 'zoom',
            'period_type' => 'month_year',
            'month' => 10,
            'year' => 2026,
        ]));

        $response->assertOk();
    }

    public function test_admin_can_export_by_yearly_period(): void
    {
        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'sipt',
            'category_id' => 1,
            'subject' => 'Kendala SIPT Tahunan',
            'description' => 'Aduan tahun 2026.',
            'status' => 'selesai',
        ]);
        $ticket->created_at = '2026-05-10 11:00:00';
        $ticket->save();

        $responsePdf = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'pdf',
            'category' => 'all',
            'period_type' => 'yearly',
            'year' => 2026,
        ]));
        $responsePdf->assertOk();

        $responseExcel = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'excel',
            'category' => 'all',
            'period_type' => 'yearly',
            'year' => 2026,
        ]));
        $responseExcel->assertOk();
    }

    public function test_admin_can_export_by_custom_date_range(): void
    {
        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'internet',
            'category_id' => 7,
            'subject' => 'Koneksi WiFi Ruang Rapat Putus',
            'description' => 'Access point tidak merespon koneksi.',
            'status' => 'pending',
        ]);
        $ticket->created_at = '2026-10-03 14:00:00';
        $ticket->save();

        $response = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'excel',
            'category' => 'internet',
            'period_type' => 'custom',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-05',
        ]));

        $response->assertOk();
    }

    public function test_admin_can_export_by_status(): void
    {
        Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'install_windows',
            'category_id' => 10,
            'subject' => 'Install Ulang Laptop Dinas',
            'description' => 'Laptop terjangkit malware.',
            'status' => 'ditolak',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'pdf',
            'category' => 'all',
            'status' => 'ditolak',
        ]));

        $response->assertOk();
    }

    public function test_admin_can_export_tickets_handled_by_themselves(): void
    {
        $otherAdmin = User::factory()->create([
            'name' => 'Admin Lainnya',
            'email' => 'other_admin_' . uniqid() . '@bbpom.go.id',
            'role' => 'admin',
        ]);

        // Ticket handled by this->admin
        $ticketMine = Ticket::create([
            'user_id' => $this->regularUser->id,
            'handled_by_id' => $this->admin->id,
            'service_key' => 'zoom',
            'category_id' => 5,
            'subject' => 'Tiket Saya Sendiri',
            'description' => 'Aduan zoom ditangani admin penguji.',
            'status' => 'diproses',
        ]);

        // Ticket handled by otherAdmin
        $ticketOther = Ticket::create([
            'user_id' => $this->regularUser->id,
            'handled_by_id' => $otherAdmin->id,
            'service_key' => 'zoom',
            'category_id' => 5,
            'subject' => 'Tiket Admin Lain',
            'description' => 'Aduan zoom ditangani admin lain.',
            'status' => 'diproses',
        ]);

        // Ticket unassigned
        $ticketUnassigned = Ticket::create([
            'user_id' => $this->regularUser->id,
            'handled_by_id' => null,
            'service_key' => 'zoom',
            'category_id' => 5,
            'subject' => 'Tiket Belum Ditugaskan',
            'description' => 'Aduan zoom belum ada petugas.',
            'status' => 'pending',
        ]);

        // Export 'me'
        $responseMe = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'excel',
            'category' => 'all',
            'handled_by' => 'me',
        ]));
        $responseMe->assertOk();

        // Export 'unassigned'
        $responseUnassigned = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'pdf',
            'category' => 'all',
            'handled_by' => 'unassigned',
        ]));
        $responseUnassigned->assertOk();

        // Export specific other admin
        $responseOther = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'excel',
            'category' => 'all',
            'handled_by' => (string) $otherAdmin->id,
        ]));
        $responseOther->assertOk();
    }

    public function test_exported_ticket_descriptions_have_no_markdown_asterisks(): void
    {
        $rawDescription = "**Layanan:** Internet & Jaringan\n**Sub Kategori:** Keluhan Internet Lemot / Mati\n### Rincian Kebutuhan:\n* **Lokasi Ruangan:** Ruang Rapat Lt. 2";
        $cleaned = \App\Services\Export\TicketQueryBuilder::stripMarkdown($rawDescription);

        $this->assertStringNotContainsString('**', $cleaned);
        $this->assertStringNotContainsString('###', $cleaned);
        $this->assertStringContainsString('Layanan: Internet & Jaringan', $cleaned);
        $this->assertStringContainsString('Sub Kategori: Keluhan Internet Lemot / Mati', $cleaned);
        $this->assertStringContainsString('Lokasi Ruangan: Ruang Rapat Lt. 2', $cleaned);

        // Also test PDF and Excel export with this ticket
        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'internet',
            'category_id' => 7,
            'subject' => '**Subjek Kendala:** WiFi Mati',
            'description' => $rawDescription,
            'status' => 'diproses',
        ]);

        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'pdf',
            'category' => 'internet',
        ]));
        $pdfResponse->assertOk();

        $excelResponse = $this->actingAs($this->admin)->get(route('admin.tickets.export', [
            'format' => 'excel',
            'category' => 'internet',
        ]));
        $excelResponse->assertOk();
    }

    public function test_filament_list_tickets_has_export_action(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ListTickets::class)
            ->assertActionExists('export')
            ->callAction('export', [
                'format' => 'pdf',
                'category' => 'all',
                'period_type' => 'all',
                'status' => 'all',
                'handled_by' => (string) $this->admin->id,
            ])
            ->assertHasNoActionErrors();
    }

    public function test_admin_can_visit_tickets_index_and_see_export_action(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/tickets');
        $response->assertOk();
        $response->assertSee('Ekspor Rekapitulasi Aduan');
    }

    public function test_admin_ticket_edit_page_strips_markdown_asterisks(): void
    {
        $rawDescription = "**Layanan:** Internet & Jaringan\n**Sub Kategori:** Keluhan Internet Lemot / Mati\n### Rincian Kebutuhan:\n* **Lokasi Ruangan:** Lab Mikrobiologi";
        $ticket = Ticket::create([
            'user_id' => $this->regularUser->id,
            'service_key' => 'internet',
            'category_id' => 7,
            'subject' => '**Aduan Jaringan:** WiFi Terputus',
            'description' => $rawDescription,
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/tickets/{$ticket->id}/edit");
        $response->assertOk();
        $response->assertSee('Layanan: Internet &amp; Jaringan', false);
        $response->assertSee('Sub Kategori: Keluhan Internet Lemot', false);
        $response->assertSee('Lokasi Ruangan: Lab Mikrobiologi', false);
        $response->assertDontSee('**Layanan:**');
        $response->assertDontSee('**Sub Kategori:**');
        $response->assertDontSee('### Rincian Kebutuhan');

        // Test Livewire component state on edit page
        Livewire::actingAs($this->admin)
            ->test(EditTicket::class, ['record' => $ticket->id])
            ->assertSchemaStateSet([
                'subject' => 'Aduan Jaringan: WiFi Terputus',
                'description' => \App\Services\Export\TicketQueryBuilder::stripMarkdown($rawDescription),
            ]);
    }
}
