<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Throwable;

class BackupController extends Controller
{
    public function download(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (!$user || $user->role !== 'admin' || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu quản trị không đúng.',
            ], 422);
        }

        $backupOptions = ['--disable-notifications' => true];
        $backupMode = 'full';

        if ($mysqlDumpBinaryPath = $this->resolveMysqlDumpBinaryPath()) {
            $this->applyMysqlDumpBinaryPath($mysqlDumpBinaryPath);
        } else {
            $backupOptions['--only-files'] = true;
            $backupMode = 'files-only';
        }

        try {
            Artisan::call('backup:run', $backupOptions);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Lỗi khi tạo backup: ' . $e->getMessage(),
            ], 500);
        }

        $latestBackup = $this->findLatestBackupFile();

        if (!$latestBackup) {
            return response()->json([
                'message' => 'Không tìm thấy file backup nào sau khi tạo.',
            ], 500);
        }

        session([
            'backup_download_path' => $latestBackup->getPathname(),
            'backup_download_name' => $latestBackup->getFilename(),
        ]);

        return response()->json([
            'message' => $backupMode === 'full'
                ? 'Backup dữ liệu đã được tạo thành công.'
                : 'Backup file đã được tạo thành công. Máy chưa có mysqldump nên phần cơ sở dữ liệu chưa được sao lưu.',
            'download_url' => route('backup.download-file'),
            'file_name' => $latestBackup->getFilename(),
            'backup_mode' => $backupMode,
        ]);
    }

    public function downloadFile(Request $request)
    {
        $filePath = session('backup_download_path');

        if (!$filePath || !File::exists($filePath)) {
            return back()->with('error', 'Không tìm thấy file backup để tải xuống.');
        }

        $downloadName = session('backup_download_name', basename($filePath));
        session()->forget(['backup_download_path', 'backup_download_name']);

        return response()->download($filePath, $downloadName);
    }

    private function findLatestBackupFile()
    {
        $backupName = config('backup.backup.name', config('app.name', 'laravel-backup'));
        $candidateDirectories = [
            storage_path('app/' . $backupName),
            storage_path('app'),
        ];

        foreach ($candidateDirectories as $directory) {
            if (!File::exists($directory)) {
                continue;
            }

            $latestFile = collect(File::allFiles($directory))
                ->filter(fn ($file) => strtolower($file->getExtension()) === 'zip')
                ->sortByDesc(fn ($file) => $file->getMTime())
                ->first();

            if ($latestFile) {
                return $latestFile;
            }
        }

        return null;
    }

    private function resolveMysqlDumpBinaryPath(): ?string
    {
        $configuredPath = trim((string) env('BACKUP_MYSQLDUMP_PATH', ''));

        if ($configuredPath !== '') {
            $normalizedPath = rtrim($configuredPath, "\\/");

            if (File::exists($normalizedPath) || File::exists($normalizedPath . DIRECTORY_SEPARATOR . 'mysqldump.exe')) {
                return $normalizedPath;
            }
        }

        $output = [];
        $exitCode = 1;

        @exec('where mysqldump 2>NUL', $output, $exitCode);

        if ($exitCode === 0 && !empty($output[0])) {
            $binaryPath = trim($output[0]);
            return rtrim(dirname($binaryPath), "\\/");
        }

        return null;
    }

    private function applyMysqlDumpBinaryPath(string $binaryPath): void
    {
        $normalizedPath = rtrim($binaryPath, "\\/") . DIRECTORY_SEPARATOR;

        foreach (array_keys(config('database.connections', [])) as $connectionName) {
            $connection = config("database.connections.{$connectionName}");

            if (!is_array($connection)) {
                continue;
            }

            $driver = $connection['driver'] ?? null;

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                config(["database.connections.{$connectionName}.dump.dump_binary_path" => $normalizedPath]);
            }
        }
    }
}