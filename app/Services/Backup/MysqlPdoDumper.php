<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;

class MysqlPdoDumper
{
    /**
     * Fallback dump database MySQL via PDO ketika mysqldump CLI gagal atau tidak tersedia.
     *
     * @param  array<string, mixed>  $connection
     */
    public function dump(array $connection, string $sqlPath): void
    {
        $pdo = DB::connection()->getPdo();
        $dbName = (string) ($connection['database'] ?? DB::getDatabaseName());
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        $handle = fopen($sqlPath, 'w');
        if ($handle === false) {
            throw new RuntimeException("Tidak dapat membuat file dump SQL di: {$sqlPath}");
        }

        try {
            $this->writeHeader($handle, $dbName);
            $tablesQuery = $isSqlite
                ? "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"
                : "SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'";
            $tablesStmt = $pdo->query($tablesQuery);
            $tables = $tablesStmt ? $tablesStmt->fetchAll(PDO::FETCH_NUM) : [];

            foreach ($tables as $tableRow) {
                $this->dumpTable($handle, $pdo, (string) $tableRow[0], $isSqlite);
            }

            fwrite($handle, "\n/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n");
            fwrite($handle, "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n");
            fwrite($handle, "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n");
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param  resource  $handle
     */
    private function writeHeader($handle, string $dbName): void
    {
        fwrite($handle, "-- ELITE MySQL Database Dump (PDO Fallback)\n");
        fwrite($handle, '-- Generated: '.now('Asia/Jakarta')->toIso8601String()."\n");
        fwrite($handle, "-- Database: `{$dbName}`\n\n");
        fwrite($handle, "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
        fwrite($handle, "/*!40101 SET NAMES utf8mb4 */;\n");
        fwrite($handle, "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n");
        fwrite($handle, "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n\n");
    }

    /**
     * @param  resource  $handle
     */
    private function dumpTable($handle, PDO $pdo, string $tableName, bool $isSqlite): void
    {
        $escapedTable = '`'.str_replace('`', '``', $tableName).'`';
        fwrite($handle, "\n-- Table structure for {$escapedTable}\nDROP TABLE IF EXISTS {$escapedTable};\n");

        $createSql = $isSqlite
            ? ($pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name = ".$pdo->quote($tableName))->fetch(PDO::FETCH_ASSOC)['sql'] ?? null)
            : ($pdo->query("SHOW CREATE TABLE {$escapedTable}")->fetch(PDO::FETCH_ASSOC)['Create Table'] ?? null);

        if ($createSql) {
            fwrite($handle, $createSql.";\n\n");
        }

        $rowsStmt = $pdo->query("SELECT * FROM {$escapedTable}");
        if (! $rowsStmt) {
            return;
        }

        $rowsStmt->setFetchMode(PDO::FETCH_ASSOC);
        $batch = [];
        $cols = null;

        while ($row = $rowsStmt->fetch()) {
            $cols ??= array_map(fn ($c) => '`'.str_replace('`', '``', (string) $c).'`', array_keys($row));
            $batch[] = '('.implode(', ', array_map(
                fn ($v) => $v === null ? 'NULL' : ((is_int($v) || is_float($v)) ? (string) $v : $pdo->quote((string) $v)),
                $row
            )).')';

            if (count($batch) >= 200) {
                fwrite($handle, "INSERT INTO {$escapedTable} (".implode(', ', $cols).") VALUES\n".implode(",\n", $batch).";\n");
                $batch = [];
            }
        }

        if ($batch !== [] && $cols !== null) {
            fwrite($handle, "INSERT INTO {$escapedTable} (".implode(', ', $cols).") VALUES\n".implode(",\n", $batch).";\n");
        }
    }
}
