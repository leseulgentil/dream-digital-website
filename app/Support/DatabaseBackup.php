<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class DatabaseBackup
{
    public function create(?string $connectionName = null, ?string $targetDirectory = null): string
    {
        $connectionName ??= (string) config('database.default');
        $connection = DB::connection($connectionName);
        $config = $connection->getConfig();
        $driver = $config['driver'] ?? null;
        $targetDirectory ??= (string) config('dream-digital.launch.backups.path');

        File::ensureDirectoryExists($targetDirectory, 0750, true);

        $database = (string) ($config['database'] ?? $connectionName);
        $safeDatabaseName = $this->safeFileSegment($database ?: $connectionName);
        $timestamp = now()->format('Ymd-His');

        return match ($driver) {
            'sqlite' => $this->backupSqlite($config, $targetDirectory, $safeDatabaseName, $timestamp),
            'mysql', 'mariadb' => $this->backupMysql($config, $targetDirectory, $safeDatabaseName, $timestamp),
            'pgsql' => $this->backupPostgres($config, $targetDirectory, $safeDatabaseName, $timestamp),
            default => throw new RuntimeException("Unsupported database backup driver: {$driver}"),
        };
    }

    private function backupSqlite(array $config, string $targetDirectory, string $safeDatabaseName, string $timestamp): string
    {
        $databasePath = (string) ($config['database'] ?? '');

        if ($databasePath === '' || $databasePath === ':memory:' || ! is_file($databasePath)) {
            throw new RuntimeException('SQLite backup requires a real database file; :memory: cannot be copied.');
        }

        $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . "{$safeDatabaseName}-{$timestamp}.sqlite";

        if (! File::copy($databasePath, $targetPath)) {
            throw new RuntimeException("Unable to copy SQLite database to {$targetPath}");
        }

        return $targetPath;
    }

    private function backupMysql(array $config, string $targetDirectory, string $safeDatabaseName, string $timestamp): string
    {
        $database = (string) ($config['database'] ?? '');

        if ($database === '') {
            throw new RuntimeException('MySQL backup requires DB_DATABASE to be configured.');
        }

        $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . "{$safeDatabaseName}-{$timestamp}.sql";
        $binary = (string) config('dream-digital.launch.backups.mysqldump_binary', 'mysqldump');

        $command = [
            $binary,
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--default-character-set=' . (string) ($config['charset'] ?? 'utf8mb4'),
        ];

        if (! empty($config['unix_socket'])) {
            $command[] = '--socket=' . $config['unix_socket'];
        } else {
            $command[] = '--host=' . (string) ($config['host'] ?? '127.0.0.1');
            $command[] = '--port=' . (string) ($config['port'] ?? '3306');
        }

        if (! empty($config['username'])) {
            $command[] = '--user=' . $config['username'];
        }

        $command[] = $database;

        $env = [];
        if (($config['password'] ?? '') !== '') {
            $env['MYSQL_PWD'] = (string) $config['password'];
        }

        $handle = fopen($targetPath, 'wb');
        if ($handle === false) {
            throw new RuntimeException("Unable to open backup file for writing: {$targetPath}");
        }

        $stderr = '';
        $process = new Process($command, base_path(), $env, null, 600);
        $exitCode = $process->run(function (string $type, string $buffer) use ($handle, &$stderr): void {
            if ($type === Process::OUT) {
                fwrite($handle, $buffer);
                return;
            }

            $stderr .= $buffer;
        });

        fclose($handle);

        if ($exitCode !== 0 || ! is_file($targetPath) || filesize($targetPath) === 0) {
            @unlink($targetPath);

            $details = trim($stderr) ?: 'mysqldump failed without stderr output';
            throw new RuntimeException('Database backup failed: ' . mb_substr($details, 0, 500));
        }

        return $targetPath;
    }

    private function backupPostgres(array $config, string $targetDirectory, string $safeDatabaseName, string $timestamp): string
    {
        $database = (string) ($config['database'] ?? '');

        if ($database === '') {
            throw new RuntimeException('PostgreSQL backup requires DB_DATABASE to be configured.');
        }

        $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . "{$safeDatabaseName}-{$timestamp}.sql";
        $binary = (string) config('dream-digital.launch.backups.pg_dump_binary', 'pg_dump');

        $command = [
            $binary,
            '--clean',
            '--if-exists',
            '--no-owner',
            '--no-privileges',
            '--format=plain',
            '--host=' . (string) ($config['host'] ?? '127.0.0.1'),
            '--port=' . (string) ($config['port'] ?? '5432'),
        ];

        if (! empty($config['username'])) {
            $command[] = '--username=' . $config['username'];
        }

        $command[] = $database;

        $env = [];
        if (($config['password'] ?? '') !== '') {
            $env['PGPASSWORD'] = (string) $config['password'];
        }

        $handle = fopen($targetPath, 'wb');
        if ($handle === false) {
            throw new RuntimeException("Unable to open backup file for writing: {$targetPath}");
        }

        $stderr = '';
        $process = new Process($command, base_path(), $env, null, 600);
        $exitCode = $process->run(function (string $type, string $buffer) use ($handle, &$stderr): void {
            if ($type === Process::OUT) {
                fwrite($handle, $buffer);
                return;
            }

            $stderr .= $buffer;
        });

        fclose($handle);

        if ($exitCode !== 0 || ! is_file($targetPath) || filesize($targetPath) === 0) {
            @unlink($targetPath);

            $details = trim($stderr) ?: 'pg_dump failed without stderr output';
            throw new RuntimeException('Database backup failed: ' . mb_substr($details, 0, 500));
        }

        return $targetPath;
    }

    private function safeFileSegment(string $value): string
    {
        $safe = preg_replace('/[^A-Za-z0-9_.-]+/', '-', $value) ?: 'database';

        return trim($safe, '-_.') ?: 'database';
    }
}
