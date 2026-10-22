<?php

namespace App\Services\Tickets;

use App\Services\Tickets\Concerns\CompilesServiceSpecificPayloads;
use Illuminate\Http\Request;

class TicketDynamicPayloadCompiler
{
    use CompilesServiceSpecificPayloads;

    public static function extractStructuredPayload(?string $desc): array
    {
        return TicketStructuredDescriptionParser::parse($desc);
    }

    public static function getFieldIcon(string $key): string
    {
        return TicketStructuredDescriptionParser::resolveFieldIcon($key);
    }

    public function compile(Request $request, ?array $service, ?string $subKey): array
    {
        if (!$service) {
            return [$request->input('subject'), $request->input('description')];
        }

        $serviceName = $service['name'] ?? 'Layanan TI';
        $subLabel = ($service['subcategories'] ?? [])[$subKey] ?? ($request->input('custom_subcategory') ?: 'Umum');
        $lines = [
            "**Layanan:** {$serviceName}",
            "**Sub Kategori:** {$subLabel}",
            "**Tanggal Pengajuan:** " . now()->format('d/m/Y H:i'),
            "**Nama Pemohon:** " . ($request->user() ? $request->user()->name : '-'),
            "---",
            "### Rincian Kebutuhan / Formulir:",
        ];

        $subjectSuffix = match ($service['id'] ?? '') {
            'sipt' => $this->compileSipt($request, $subKey, $lines),
            'srikandi' => $this->compileSrikandi($request, $subKey, $lines),
            'email_corporate' => $this->compileEmail($request, $subKey, $lines),
            'internet' => $this->compileInternet($request, $subKey, $lines),
            'subsite' => $this->compileSubsite($request, $subKey, $lines),
            'infografis' => $this->compileInfografis($request, $lines),
            default => $this->compileDefault($request, $subLabel, $lines),
        };

        return [$request->input('subject') ?: ($subjectSuffix ?: $subLabel), implode("\n", $lines)];
    }
}
