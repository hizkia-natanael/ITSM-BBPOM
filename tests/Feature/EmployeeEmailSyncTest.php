<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketBsreDetail;
use App\Models\TicketResponse;
use App\Models\User;
use App\Services\Users\EmployeeEmailSyncService;
use Database\Seeders\Support\OfficialEmployeeEmails;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeEmailSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_official_employee_emails_contains_149_unique_valid_pom_emails(): void
    {
        $map = OfficialEmployeeEmails::all();

        $this->assertCount(149, $map);
        $this->assertCount(149, array_unique(array_values($map)));

        foreach ($map as $nip => $email) {
            $this->assertNotEmpty((string) $nip);
            $this->assertNotFalse(filter_var($email, FILTER_VALIDATE_EMAIL), "Email tidak valid: {$email}");
            $this->assertStringEndsWith('@pom.go.id', $email);
        }

        // Pastikan 2 pasang nama berdekatan yang rawan tertukar di Excel sudah selaras
        $this->assertSame('nuridah.amin@pom.go.id', $map['198002262006042002']);
        $this->assertSame('nur.rahmawati@pom.go.id', $map['197911102006042003']);
        $this->assertSame('tri.pujiati@pom.go.id', $map['197012262000032001']);
        $this->assertSame('tri.pujiasih@pom.go.id', $map['197312011996032001']);
    }

    public function test_in_place_email_sync_preserves_tickets_chats_passwords_and_updates_ticket_references(): void
    {
        $customPasswordHash = Hash::make('PasswordKhususServer2026!');

        $pegawai = User::create([
            'nip' => '199504252019032004',
            'name' => 'Annisa Aprilia Wahyu Dhinari, S.H',
            'email' => 'annisa.a@bbpom.go.id',
            'role' => 'user',
            'password' => $customPasswordHash,
        ]);
        $admin = User::create([
            'nip' => '197012262000032001',
            'name' => 'Tri Pujiati, S.Kom',
            'email' => 'tri.p@bbpom.go.id',
            'role' => 'admin',
            'password' => $customPasswordHash,
        ]);

        $originalPegawaiId = $pegawai->id;
        $originalAdminId = $admin->id;
        $category = Category::firstOrCreate(['slug' => 'sertifikat-bsre'], ['name' => 'Sertifikat BSrE']);
        $ticket = Ticket::create([
            'ticket_number' => 'TIK-SYNC-001',
            'user_id' => $pegawai->id,
            'handled_by_id' => $admin->id,
            'category_id' => $category->id,
            'service_key' => 'sertifikat_bse',
            'subcategory' => 'reset_password',
            'subject' => 'Reset Password BSrE',
            'description' => "Detail Permohonan:\n• Nama: Annisa\n• Email: annisa.a@bbpom.go.id\n• Kendala lupa passphrase",
            'status' => 'diproses',
        ]);
        TicketBsreDetail::create([
            'ticket_id' => $ticket->id,
            'sub_category' => 'reset_password',
            'tanggal_pengajuan' => now()->toDateString(),
            'nama' => $pegawai->name,
            'nip' => $pegawai->nip,
            'email' => 'annisa.a@bbpom.go.id',
            'deskripsi' => 'Lupa passphrase',
        ]);
        TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $pegawai->id, 'response_text' => 'Mohon bantuannya untuk reset passphrase saya.']);
        TicketResponse::create(['ticket_id' => $ticket->id, 'user_id' => $admin->id, 'response_text' => 'Baik Bu Annisa, sedang kami proses ke portal BSrE.']);

        // Eksekusi sinkronisasi & UserSeeder (mensimulasikan UPDATE-SERVER.bat)
        $stats = app(EmployeeEmailSyncService::class)->sync();
        $this->assertSame(2, $stats['updated_users']);
        $this->assertSame(1, $stats['updated_bsre_details']);
        $this->assertSame(1, $stats['updated_ticket_descriptions']);

        $this->seed(UserSeeder::class);
        $pegawai->refresh();
        $admin->refresh();
        $ticket->refresh();

        $this->assertSame($originalPegawaiId, $pegawai->id);
        $this->assertSame($originalAdminId, $admin->id);
        $this->assertSame('annisa.dhinari@pom.go.id', $pegawai->email);
        $this->assertSame('tri.pujiati@pom.go.id', $admin->email);
        $this->assertTrue(Hash::check('PasswordKhususServer2026!', $pegawai->password));
        $this->assertSame('admin', $admin->role);
        $this->assertSame('annisa.dhinari@pom.go.id', $ticket->user->email);
        $this->assertSame('annisa.dhinari@pom.go.id', $ticket->bsreDetail->email);
        $this->assertStringContainsString('annisa.dhinari@pom.go.id', $ticket->description);
        $this->assertStringNotContainsString('annisa.a@bbpom.go.id', $ticket->description);
        $this->assertCount(2, $ticket->responses);
    }
}
