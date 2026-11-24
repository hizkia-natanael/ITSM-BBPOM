<?php

namespace App\Services;

use App\Models\BackupSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleDriveService
{
    private const OAUTH_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const DRIVE_FILES_URL = 'https://www.googleapis.com/drive/v3/files';

    private const DRIVE_UPLOAD_URL = 'https://www.googleapis.com/upload/drive/v3/files';

    private const DRIVE_ABOUT_URL = 'https://www.googleapis.com/drive/v3/about';

    private const CHUNK_SIZE_BYTES = 5242880; // 5 MiB (kelipatan tepat 256 KiB untuk Google Drive API)

    public function hasOAuthClient(): bool
    {
        $cfg = BackupSetting::effectiveConfig();

        return $cfg['gdrive_client_id'] !== '' && $cfg['gdrive_client_secret'] !== '';
    }

    public function isConfigured(): bool
    {
        $cfg = BackupSetting::effectiveConfig();

        return $this->hasOAuthClient() && $cfg['gdrive_refresh_token'] !== '';
    }

    public function getAuthorizationUrl(string $redirectUri, string $state): string
    {
        $cfg = BackupSetting::effectiveConfig();

        if (! $this->hasOAuthClient()) {
            throw new RuntimeException('Google Drive Client ID dan Client Secret belum diisi.');
        }

        $query = http_build_query([
            'client_id' => $cfg['gdrive_client_id'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/drive',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);

        return self::OAUTH_AUTH_URL.'?'.$query;
    }

    /**
     * @return array{refresh_token: string, access_token: string, email: string}
     */
    public function exchangeCode(string $code, string $redirectUri): array
    {
        $cfg = BackupSetting::effectiveConfig();

        $response = Http::asForm()
            ->timeout(30)
            ->post(self::OAUTH_TOKEN_URL, [
                'code' => $code,
                'client_id' => $cfg['gdrive_client_id'],
                'client_secret' => $cfg['gdrive_client_secret'],
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal menukar kode OAuth Google: '.$response->body());
        }

        $data = $response->json();
        $refreshToken = (string) ($data['refresh_token'] ?? '');
        $accessToken = (string) ($data['access_token'] ?? '');

        if ($refreshToken === '' || $accessToken === '') {
            throw new RuntimeException('Google tidak mengembalikan refresh_token. Pastikan consent disetujui ulang.');
        }

        $email = $this->fetchAccountEmail($accessToken);

        return [
            'refresh_token' => $refreshToken,
            'access_token' => $accessToken,
            'email' => $email,
        ];
    }

    public function fetchAccessToken(): string
    {
        $cfg = BackupSetting::effectiveConfig();

        if (! $this->isConfigured()) {
            throw new RuntimeException('Kredensial Google Drive (Client ID, Secret, Refresh Token) belum lengkap.');
        }

        $response = Http::asForm()
            ->timeout(30)
            ->post(self::OAUTH_TOKEN_URL, [
                'client_id' => $cfg['gdrive_client_id'],
                'client_secret' => $cfg['gdrive_client_secret'],
                'refresh_token' => $cfg['gdrive_refresh_token'],
                'grant_type' => 'refresh_token',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal memperbarui access token Google Drive: '.$response->body());
        }

        $accessToken = (string) $response->json('access_token', '');
        if ($accessToken === '') {
            throw new RuntimeException('Access token Google Drive kosong.');
        }

        return $accessToken;
    }

    /**
     * Upload file ZIP ke Google Drive menggunakan Resumable Upload API v3.
     *
     * @return array{id: string, webViewLink: string}
     */
    public function uploadZipFile(string $zipPath, string $filename): array
    {
        if (! is_file($zipPath)) {
            throw new RuntimeException("File ZIP tidak ditemukan: {$zipPath}");
        }

        $fileSize = filesize($zipPath);
        if ($fileSize === false || $fileSize <= 0) {
            throw new RuntimeException("File ZIP kosong atau gagal dibaca: {$zipPath}");
        }

        $accessToken = $this->fetchAccessToken();
        $folderId = $this->ensureTargetFolderId($accessToken);

        $metadata = ['name' => $filename];
        if ($folderId !== '') {
            $metadata['parents'] = [$folderId];
        }

        $initResponse = Http::withToken($accessToken)
            ->timeout(30)
            ->withHeaders([
                'X-Upload-Content-Type' => 'application/zip',
                'X-Upload-Content-Length' => (string) $fileSize,
            ])
            ->post(self::DRIVE_UPLOAD_URL.'?uploadType=resumable&fields=id,webViewLink', $metadata);

        if (! $initResponse->successful()) {
            throw new RuntimeException('Gagal memulai sesi upload Google Drive: '.$initResponse->body());
        }

        $uploadUri = $initResponse->header('Location');
        if (empty($uploadUri)) {
            throw new RuntimeException('Header Location sesi resumable upload Google Drive tidak ditemukan.');
        }

        $handle = fopen($zipPath, 'rb');
        if ($handle === false) {
            throw new RuntimeException("Tidak dapat membuka file ZIP untuk upload: {$zipPath}");
        }

        try {
            $offset = 0;
            $uploadedFile = null;

            while ($offset < $fileSize) {
                $chunk = fread($handle, self::CHUNK_SIZE_BYTES);
                if ($chunk === false) {
                    throw new RuntimeException('Gagal membaca potongan file ZIP saat upload.');
                }

                $chunkLen = strlen($chunk);
                $end = $offset + $chunkLen - 1;

                $chunkResponse = Http::timeout(120)
                    ->withHeaders([
                        'Content-Length' => (string) $chunkLen,
                        'Content-Range' => "bytes {$offset}-{$end}/{$fileSize}",
                    ])
                    ->withBody($chunk, 'application/zip')
                    ->put($uploadUri);

                $status = $chunkResponse->status();

                if ($status === 200 || $status === 201) {
                    $uploadedFile = $chunkResponse->json();
                    break;
                }

                if ($status !== 308) {
                    throw new RuntimeException("Upload Google Drive gagal (HTTP {$status}): ".$chunkResponse->body());
                }

                $offset += $chunkLen;
            }
        } finally {
            fclose($handle);
        }

        $fileId = (string) ($uploadedFile['id'] ?? '');
        if ($fileId === '') {
            throw new RuntimeException('Upload selesai tetapi ID file Google Drive tidak diterima.');
        }

        return [
            'id' => $fileId,
            'webViewLink' => (string) ($uploadedFile['webViewLink'] ?? "https://drive.google.com/file/d/{$fileId}/view"),
        ];
    }

    private function ensureTargetFolderId(string $accessToken): string
    {
        $cfg = BackupSetting::effectiveConfig();
        if ($cfg['gdrive_folder_id'] !== '') {
            return $cfg['gdrive_folder_id'];
        }

        $folderName = $cfg['gdrive_folder_name'] ?: 'Backup-ELITE';
        $escapedName = str_replace("'", "\\'", $folderName);
        $query = "name = '{$escapedName}' and mimeType = 'application/vnd.google-apps.folder' and trashed = false";

        $search = Http::withToken($accessToken)
            ->timeout(30)
            ->get(self::DRIVE_FILES_URL, [
                'q' => $query,
                'fields' => 'files(id,name)',
                'pageSize' => 1,
            ]);

        if ($search->successful()) {
            $existingId = (string) ($search->json('files.0.id') ?? '');
            if ($existingId !== '') {
                $this->persistFolderId($existingId);

                return $existingId;
            }
        }

        $create = Http::withToken($accessToken)
            ->timeout(30)
            ->post(self::DRIVE_FILES_URL.'?fields=id', [
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
            ]);

        if (! $create->successful()) {
            throw new RuntimeException('Gagal membuat folder Google Drive: '.$create->body());
        }

        $createdId = (string) $create->json('id', '');
        if ($createdId !== '') {
            $this->persistFolderId($createdId);
        }

        return $createdId;
    }

    private function persistFolderId(string $folderId): void
    {
        try {
            $setting = BackupSetting::query()->first();
            if ($setting && empty($setting->gdrive_folder_id)) {
                $setting->update(['gdrive_folder_id' => $folderId]);
            }
        } catch (\Throwable) {
            // Abaikan jika tabel belum tersedia
        }
    }

    private function fetchAccountEmail(string $accessToken): string
    {
        try {
            $about = Http::withToken($accessToken)
                ->timeout(15)
                ->get(self::DRIVE_ABOUT_URL, ['fields' => 'user(emailAddress)']);

            if ($about->successful()) {
                return (string) ($about->json('user.emailAddress') ?? '');
            }
        } catch (\Throwable) {
            // Optional metadata
        }

        return '';
    }
}
