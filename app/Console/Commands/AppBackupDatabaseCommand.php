<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AppBackupDatabaseCommand extends Command
{
    protected $signature = 'app:db-backup {--limit=10 : Number of recent backups to keep}';

    protected $description = 'Backup the SQLite database file';

    public function handle(): int
    {
        $dbPath = config('database.connections.sqlite.database');

        if (!File::exists($dbPath)) {
            $this->error("Database file not found at: {$dbPath}");
            return self::FAILURE;
        }

        $backupDir = database_path('backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'database_' . now()->format('Ymd_His') . '.sqlite';
        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        if (File::copy($dbPath, $backupPath)) {
            $this->info("Database backed up successfully to: {$backupPath}");
            
            $this->cleanupOldBackups($backupDir, (int) $this->option('limit'));
            
            return self::SUCCESS;
        }

        $this->error("Failed to backup database.");
        return self::FAILURE;
    }

    private function cleanupOldBackups(string $backupDir, int $limit): void
    {
        $files = File::glob($backupDir . DIRECTORY_SEPARATOR . 'database_*.sqlite');
        
        if (count($files) <= $limit) {
            return;
        }

        // Sort files by modified time (oldest first)
        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        $toDelete = count($files) - $limit;
        for ($i = 0; $i < $toDelete; $i++) {
            File::delete($files[$i]);
            $this->line("Deleted old backup: " . basename($files[$i]));
        }
    }
}
