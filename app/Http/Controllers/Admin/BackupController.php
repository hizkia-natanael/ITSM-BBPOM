<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Backup\RunManualBackupAction;
use App\Http\Controllers\Controller;
use App\Models\BackupSetting;
use App\Models\DatabaseBackup;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupController extends Controller
{
    private function ensureAdmin(): User
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        return $user;
    }

    public function index(): RedirectResponse
    {
        $this->ensureAdmin();

        return redirect('/admin/backup');
    }

    public function runManual(RunManualBackupAction $action): RedirectResponse
    {
        $user = $this->ensureAdmin();
        $result = $action->execute($user);

        return redirect()->back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $this->ensureAdmin();

        $validated = $request->validate([
            'auto_backup_enabled' => ['required', 'boolean'],
            'schedule_time' => ['required', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'retention_days' => ['required', 'integer', 'min:1', 'max:365'],
            'filename_prefix' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'include_documents' => ['required', 'boolean'],
            'gdrive_client_id' => ['nullable', 'string', 'max:255'],
            'gdrive_client_secret' => ['nullable', 'string', 'max:255'],
            'gdrive_refresh_token' => ['nullable', 'string', 'max:512'],
            'gdrive_folder_id' => ['nullable', 'string', 'max:128'],
            'gdrive_folder_name' => ['required', 'string', 'max:128'],
        ]);

        $setting = BackupSetting::query()->first() ?? new BackupSetting;

        $setting->auto_backup_enabled = (bool) $validated['auto_backup_enabled'];
        $setting->schedule_time = $validated['schedule_time'];
        $setting->retention_days = (int) $validated['retention_days'];
        $setting->filename_prefix = $validated['filename_prefix'];
        $setting->include_documents = (bool) $validated['include_documents'];
        $setting->gdrive_client_id = trim((string) ($validated['gdrive_client_id'] ?? '')) ?: null;
        $setting->gdrive_folder_id = trim((string) ($validated['gdrive_folder_id'] ?? '')) ?: null;
        $setting->gdrive_folder_name = trim($validated['gdrive_folder_name']);
        $setting->updated_by = $user->id;

        if (! empty($validated['gdrive_client_secret'])) {
            $setting->gdrive_client_secret = trim($validated['gdrive_client_secret']);
        }

        if (! empty($validated['gdrive_refresh_token'])) {
            $setting->gdrive_refresh_token = trim($validated['gdrive_refresh_token']);
        }

        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan backup harian dan Google Drive berhasil disimpan.');
    }

    public function googleConnect(Request $request, GoogleDriveService $driveService): RedirectResponse
    {
        $this->ensureAdmin();

        if (! $driveService->hasOAuthClient()) {
            return redirect('/admin/backup')
                ->with('error', 'Simpan Google Drive Client ID dan Client Secret terlebih dahulu sebelum menghubungkan akun.');
        }

        $state = Str::random(40);
        $request->session()->put('gdrive_oauth_state', $state);

        $url = $driveService->getAuthorizationUrl(route('admin.backup.google.callback'), $state);

        return redirect()->away($url);
    }

    public function googleCallback(Request $request, GoogleDriveService $driveService): RedirectResponse
    {
        $user = $this->ensureAdmin();

        if ($request->filled('error')) {
            return redirect('/admin/backup')
                ->with('error', 'Otorisasi Google Drive dibatalkan: '.$request->string('error'));
        }

        $expectedState = (string) $request->session()->pull('gdrive_oauth_state', '');
        $incomingState = (string) $request->query('state', '');

        if ($expectedState === '' || ! hash_equals($expectedState, $incomingState)) {
            return redirect('/admin/backup')
                ->with('error', 'Sesi otorisasi Google Drive tidak valid atau kedaluwarsa. Silakan coba lagi.');
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            return redirect('/admin/backup')
                ->with('error', 'Kode otorisasi Google tidak ditemukan.');
        }

        try {
            $tokens = $driveService->exchangeCode($code, route('admin.backup.google.callback'));

            $setting = BackupSetting::query()->first() ?? new BackupSetting;
            $setting->gdrive_refresh_token = $tokens['refresh_token'];
            if ($tokens['email'] !== '') {
                $setting->gdrive_account_email = $tokens['email'];
            }
            $setting->updated_by = $user->id;
            $setting->save();

            return redirect('/admin/backup')
                ->with('success', 'Akun Google Drive berhasil dihubungkan'.($tokens['email'] ? " ({$tokens['email']})" : '').'.');
        } catch (\Throwable $e) {
            return redirect('/admin/backup')
                ->with('error', 'Gagal menghubungkan Google Drive: '.$e->getMessage());
        }
    }

    public function googleDisconnect(): RedirectResponse
    {
        $user = $this->ensureAdmin();

        $setting = BackupSetting::query()->first();
        if ($setting) {
            $setting->update([
                'gdrive_refresh_token' => null,
                'gdrive_account_email' => null,
                'updated_by' => $user->id,
            ]);
        }

        return redirect()->back()->with('success', 'Koneksi Google Drive telah diputus.');
    }

    public function download(DatabaseBackup $backup): BinaryFileResponse
    {
        $this->ensureAdmin();

        if (! $backup->hasLocalFile()) {
            abort(404, 'File backup lokal sudah dihapus atau tidak ditemukan.');
        }

        return response()->download((string) $backup->local_path, $backup->filename);
    }

    public function destroy(DatabaseBackup $backup): RedirectResponse
    {
        $this->ensureAdmin();

        if (! empty($backup->local_path) && is_file($backup->local_path)) {
            @unlink($backup->local_path);
        }

        $backup->delete();

        return redirect()->back()->with('success', "Riwayat dan file lokal '{$backup->filename}' berhasil dihapus.");
    }

    public function downloadEnv(Request $request): BinaryFileResponse|JsonResponse
    {
        $user = $this->ensureAdmin();

        $validated = $request->validate([
            'admin_password' => ['required', 'string'],
            'zip_password' => ['required', 'string', 'min:8', 'max:128'],
        ], [
            'admin_password.required' => 'Password akun Administrator wajib diisi.',
            'zip_password.required' => 'Password kunci ZIP wajib diisi.',
            'zip_password.min' => 'Password kunci ZIP minimal 8 karakter.',
        ]);

        if (! Hash::check($validated['admin_password'], (string) $user->password)) {
            return response()->json([
                'message' => 'Password akun Administrator tidak sesuai.',
                'errors' => [
                    'admin_password' => ['Password akun Administrator tidak sesuai.'],
                ],
            ], 422);
        }

        $envPath = base_path('.env');
        if (! is_file($envPath)) {
            return response()->json([
                'message' => 'File .env tidak ditemukan di server.',
            ], 404);
        }

        $tmpBase = tempnam(sys_get_temp_dir(), 'envzip_');
        if ($tmpBase === false) {
            return response()->json([
                'message' => 'Gagal menyiapkan file sementara untuk arsip .env.',
            ], 500);
        }

        $tmpZip = $tmpBase.'.zip';
        @unlink($tmpBase);

        $zip = new ZipArchive;
        if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json([
                'message' => 'Gagal membuat arsip ZIP terenkripsi.',
            ], 500);
        }

        $zip->addFile($envPath, '.env');
        $zip->setPassword($validated['zip_password']);
        $zip->setEncryptionName('.env', ZipArchive::EM_AES_256);
        $zip->close();

        Log::info('Backup .env terenkripsi diunduh oleh administrator ELITE.', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $downloadName = 'backup-env-elite-'.now()->format('Y-m-d').'.zip';

        return response()
            ->download($tmpZip, $downloadName, ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend(true);
    }
}
