<?php

namespace App\Services\Tickets\Handlers\Kegiatan;

use App\Models\Category;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KegiatanPresenceHandler
{
    public function handleDaftarHadir(Request $request, Category $category): RedirectResponse
    {
        if (!$request->filled('jam') && $request->filled('jam_mulai')) {
            $mulai = $request->input('jam_mulai');
            $selesai = $request->input('jam_selesai', 'Selesai');
            $request->merge(['jam' => "{$mulai} - {$selesai} WIB"]);
        }

        $validated = $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'nama_acara' => ['required', 'string', 'max:255'],
            'tanggal_kegiatan' => ['required', 'date'],
            'jam' => ['required', 'string', 'max:100'],
        ], [
            'nama_acara.required' => 'Nama acara wajib diisi.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'jam.required' => 'Jam / jadwal acara wajib diisi.',
        ]);

        $tglKegiatan = Carbon::parse($validated['tanggal_kegiatan'])->translatedFormat('d F Y');
        $tglPengajuan = Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y');

        $description = "Permohonan Pembuatan Presensi / Daftar Hadir Online\n\n"
            . "• Tanggal Pengajuan: {$tglPengajuan}\n"
            . "• Pemohon: {$validated['nama']}\n"
            . "• Nama Acara: {$validated['nama_acara']}\n"
            . "• Tanggal Kegiatan: {$tglKegiatan}\n"
            . "• Jam / Waktu Pelaksanaan: {$validated['jam']}";

        $ticket = $this->createTicket(
            $request,
            $category,
            'permintaan_daftar_hadir',
            "Permintaan Daftar Hadir - {$validated['nama_acara']}",
            $description
        );

        $ticket->kegiatanDetail()->create([
            'sub_category' => 'permintaan_daftar_hadir',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'nama_acara' => $validated['nama_acara'],
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'jam' => $validated['jam'],
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Permohonan Daftar Hadir berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    public function handleQrCode(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:255'],
            'link_qrcode' => ['required', 'string', 'max:1000'],
        ], [
            'link_qrcode.required' => 'Tautan / Link yang akan dijadikan QR Code wajib diisi.',
        ]);

        $tglPengajuan = Carbon::parse($validated['tanggal_pengajuan'])->translatedFormat('d F Y');
        $description = "Permohonan Generate QR Code Resmi\n\n"
            . "• Tanggal Pengajuan: {$tglPengajuan}\n"
            . "• Pemohon: {$validated['nama']}\n• Tautan Sasaran: {$validated['link_qrcode']}";

        $ticket = $this->createTicket(
            $request,
            $category,
            'permintaan_qrcode',
            "Permintaan QR Code - {$validated['nama']}",
            $description
        );

        $ticket->kegiatanDetail()->create([
            'sub_category' => 'permintaan_qrcode',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'nama' => $validated['nama'],
            'link_qrcode' => $validated['link_qrcode'],
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Permohonan QR Code berhasil diajukan dengan nomor tiket: {$ticket->ticket_number}.");
    }

    protected function createTicket(Request $request, Category $category, string $sub, string $subject, string $desc): Ticket
    {
        return Ticket::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'service_key' => 'formulir_kegiatan',
            'subcategory' => $sub,
            'subject' => $subject,
            'description' => $desc,
            'status' => 'pending',
        ]);
    }
}
