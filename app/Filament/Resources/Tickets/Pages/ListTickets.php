<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor Rekapitulasi Aduan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->modalHeading('Ekspor Rekapitulasi Aduan')
                ->modalDescription('Pilih format berkas, kategori layanan TIK, dan periode waktu yang ingin diekspor.')
                ->modalSubmitActionLabel('Unduh Berkas')
                ->modalIcon('heroicon-o-document-arrow-down')
                ->modalWidth('4xl')
                ->form([
                    // Pilihan Format Berkas (Sebelahan Kanan Kiri)
                    ViewField::make('format')
                        ->view('components.ticket-export-format-buttons')
                        ->default('pdf')
                        ->columnSpanFull(),

                    Grid::make(['default' => 1, 'md' => 2])
                        ->schema([
                            // ==========================================
                            // KOLOM KIRI: 2 FILTER (KATALOG & STATUS)
                            // ==========================================
                            Group::make([
                                Select::make('category')
                                    ->label('Katalog Layanan TIK (12 Kategori)')
                                    ->options([
                                        'all' => 'Semua Kategori (12 Katalog Lengkap)',
                                        'sipt' => '1. SIPT (Sistem Informasi Pelaporan Terpadu)',
                                        'srikandi' => '2. Srikandi (Sistem Kearsipan Dinamis Terintegrasi)',
                                        'email_corporate' => '3. Email Corporate (@bbpom.go.id)',
                                        'sertifikat_bse' => '4. Sertifikat BSrE / TTE',
                                        'zoom' => '5. Fasilitas Zoom Meeting / Webinar',
                                        'formulir_kegiatan' => '6. Formulir & Kegiatan (Presensi, QR Code, Survey)',
                                        'internet' => '7. Internet & Jaringan (WiFi, LAN, VPN)',
                                        'subsite' => '8. Pengelolaan Subsite BBPOM Semarang',
                                        'infografis' => '9. Desain Infografis & Media Publikasi',
                                        'install_windows' => '10. Install Ulang Windows PC/Laptop Dinas',
                                        'lisensi_office' => '11. Lisensi Microsoft Office (Word, Excel, PPT)',
                                        'lainnya' => '12. Layanan TIK Umum / Lainnya',
                                    ])
                                    ->default('all')
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->selectablePlaceholder(false),

                                Select::make('status')
                                    ->label('Filter Status Aduan')
                                    ->options([
                                        'all' => 'Semua Status (Seluruh Aduan)',
                                        'pending' => 'Pending (Menunggu Antrian IT)',
                                        'diproses' => 'Diproses (Sedang Ditangani Teknisi)',
                                        'selesai' => 'Selesai (Tuntas Diperbaiki)',
                                        'ditolak' => 'Ditolak (Dibatalkan / Di Luar Wewenang)',
                                    ])
                                    ->default('all')
                                    ->required()
                                    ->native(false)
                                    ->selectablePlaceholder(false),
                            ])->extraAttributes(['class' => 'space-y-4']),

                            // ==========================================
                            // KOLOM KANAN: 2 FILTER (PETUGAS IT & PERIODE)
                            // ==========================================
                            Group::make([
                                Select::make('handled_by')
                                    ->label('Petugas IT Penanganan')
                                    ->options(function () {
                                        $options = [
                                            'all' => 'Semua Petugas IT',
                                        ];

                                        $admins = \App\Models\User::where('role', 'admin')
                                            ->orderBy('name')
                                            ->get();

                                        foreach ($admins as $admin) {
                                            $options[(string) $admin->id] = $admin->id === auth()->id()
                                                ? "{$admin->name} (Saya)"
                                                : $admin->name;
                                        }

                                        return $options;
                                    })
                                    ->default('all')
                                    ->searchable()
                                    ->native(false)
                                    ->selectablePlaceholder(false),

                                Select::make('period_type')
                                    ->label('Filter Periode Waktu')
                                    ->options([
                                        'all' => 'Seluruh Aduan (Semua Waktu)',
                                        'yearly' => 'Tahunan (Pilih Tahun)',
                                        'month_year' => 'Bulanan (Bulan & Tahun)',
                                        'custom' => 'Rentang Tanggal Kustom',
                                    ])
                                    ->default('all')
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->selectablePlaceholder(false),

                                // Opsi 1: Tahunan (Hanya input tahun)
                                Select::make('year')
                                    ->label('Pilih Tahun')
                                    ->options([
                                        2024 => '2024',
                                        2025 => '2025',
                                        2026 => '2026',
                                        2027 => '2027',
                                        2028 => '2028',
                                    ])
                                    ->default((int) now()->format('Y'))
                                    ->native(false)
                                    ->visible(fn ($get) => $get('period_type') === 'yearly'),

                                // Opsi 2: Bulanan (Bulan & Tahun)
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->schema([
                                        Select::make('month')
                                            ->label('Bulan')
                                            ->options([
                                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ])
                                            ->default((int) now()->format('n'))
                                            ->native(false),

                                        Select::make('year')
                                            ->label('Tahun')
                                            ->options([
                                                2024 => '2024',
                                                2025 => '2025',
                                                2026 => '2026',
                                                2027 => '2027',
                                                2028 => '2028',
                                            ])
                                            ->default((int) now()->format('Y'))
                                            ->native(false),
                                    ])
                                    ->visible(fn ($get) => $get('period_type') === 'month_year'),

                                // Opsi 3: Rentang Tanggal Kustom
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->schema([
                                        DatePicker::make('start_date')
                                            ->label('Tanggal Mulai')
                                            ->native(false)
                                            ->maxDate(now()),

                                        DatePicker::make('end_date')
                                            ->label('Tanggal Selesai')
                                            ->native(false)
                                            ->maxDate(now()),
                                    ])
                                    ->visible(fn ($get) => $get('period_type') === 'custom'),
                            ])->extraAttributes(['class' => 'space-y-4']),
                        ])
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('admin.tickets.export', $data);
                }),
        ];
    }
}
