<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;

class TicketExportModalSchema
{
    public static function make(): array
    {
        $years = [2024 => '2024', 2025 => '2025', 2026 => '2026', 2027 => '2027', 2028 => '2028'];
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return [
            ViewField::make('format')
                ->view('components.ticket-export-format-buttons')
                ->default('pdf')
                ->columnSpanFull(),

            Grid::make(['default' => 1, 'md' => 2])
                ->schema([
                    Group::make([
                        Select::make('category')
                            ->label('Katalog Layanan TIK (12 Kategori)')
                            ->options(self::categoryOptions())
                            ->default('all')->required()->searchable()->native(false)->selectablePlaceholder(false),

                        Select::make('status')
                            ->label('Filter Status Aduan')
                            ->options([
                                'all' => 'Semua Status (Seluruh Aduan)',
                                'pending' => 'Pending (Menunggu Antrian IT)',
                                'diproses' => 'Diproses (Sedang Ditangani Teknisi)',
                                'selesai' => 'Selesai (Tuntas Diperbaiki)',
                                'ditolak' => 'Ditolak (Dibatalkan / Di Luar Wewenang)',
                            ])
                            ->default('all')->required()->native(false)->selectablePlaceholder(false),
                    ])->extraAttributes(['class' => 'space-y-4']),

                    Group::make([
                        Select::make('handled_by')
                            ->label('Petugas IT Penanganan')
                            ->options(fn () => self::adminOptions())
                            ->default('all')->searchable()->native(false)->selectablePlaceholder(false),

                        Select::make('period_type')
                            ->label('Filter Periode Waktu')
                            ->options([
                                'all' => 'Seluruh Aduan (Semua Waktu)',
                                'yearly' => 'Tahunan (Pilih Tahun)',
                                'month_year' => 'Bulanan (Bulan & Tahun)',
                                'custom' => 'Rentang Tanggal Kustom',
                            ])
                            ->default('all')->required()->live()->native(false)->selectablePlaceholder(false),

                        Select::make('year')
                            ->label('Pilih Tahun')
                            ->options($years)->default((int) now()->format('Y'))->native(false)
                            ->visible(fn ($get) => $get('period_type') === 'yearly'),

                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                Select::make('month')->label('Bulan')->options($months)->default((int) now()->format('n'))->native(false),
                                Select::make('year')->label('Tahun')->options($years)->default((int) now()->format('Y'))->native(false),
                            ])
                            ->visible(fn ($get) => $get('period_type') === 'month_year'),

                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                DatePicker::make('start_date')->label('Tanggal Mulai')->native(false)->maxDate(now()),
                                DatePicker::make('end_date')->label('Tanggal Selesai')->native(false)->maxDate(now()),
                            ])
                            ->visible(fn ($get) => $get('period_type') === 'custom'),
                    ])->extraAttributes(['class' => 'space-y-4']),
                ])
                ->columnSpanFull(),
        ];
    }

    private static function categoryOptions(): array
    {
        return [
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
        ];
    }

    private static function adminOptions(): array
    {
        $options = ['all' => 'Semua Petugas IT'];
        foreach (User::where('role', 'admin')->orderBy('name')->get() as $admin) {
            $options[(string) $admin->id] = $admin->id === auth()->id() ? "{$admin->name} (Saya)" : $admin->name;
        }
        return $options;
    }
}
