<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Backup extends BaseController
{
    public function index()
    {
        return view('admin/backup/index', [
            'title' => 'Backup & Restore'
        ]);
    }

    // Database Backup
    public function dbExport()
    {
        try {
            $dbName = 'funrun_db_' . date('Y-m-d_H-i-s') . '.sql';

            // Using mysqldump via exec if possible for reliability on large DBs,
            // but fallback to CI4 dbutil for portability if exec is disabled?
            // CI4 dbutil is safer for PHP-only envs basically.

            $db = \Config\Database::connect();

            // Simple custom dumper to handle basics
            // Note: dbutil is deprecated/removed in some CI4 versions or moved?
            // Better to use custom simple logic or shell_exec if usually on XAMPP.
            // Let's try shell_exec check for mysqldump in Windows XAMPP path

            // Fallback: Custom SQL generation
            $tables = $db->listTables();
            $sql = '-- Database Backup: ' . date('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // Structure
                $create = $db->query("SHOW CREATE TABLE `$table`")->getRowArray();
                $sql .= "DROP TABLE IF EXISTS `$table`;\n";
                $sql .= $create['Create Table'] . ";\n\n";

                // Data
                $rows = $db->table($table)->get()->getResultArray();
                foreach ($rows as $row) {
                    $cols = array_keys($row);
                    $vals = array_values($row);

                    $vals = array_map(function ($val) use ($db) {
                        return $val === null ? 'NULL' : $db->escape($val);
                    }, $vals);

                    $sql .= "INSERT INTO `$table` (`" . implode('`, `', $cols) . '`) VALUES (' . implode(', ', $vals) . ");\n";
                }
                $sql .= "\n";
            }
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            return $this->response->download($dbName, $sql);
        } catch (\Exception $e) {
            return redirect()->to('/admin/backup')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    // Database Restore
    public function dbRestore()
    {
        $file = $this->request->getFile('backup_file');

        if (!$file->isValid()) {
            return redirect()->to('/admin/backup')->with('error', 'File tidak valid.');
        }

        $sql = file_get_contents($file->getTempName());
        $db = \Config\Database::connect();

        // Split by semicolon but ignore inside quotes is hard.
        // Simplistic split might fail on data containing semicolons.
        // Better to execute line by line?

        $db->transStart();

        // Remove comments
        $lines = explode("\n", $sql);
        $templine = '';

        foreach ($lines as $line) {
            // Skip comments
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            $templine .= $line;

            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                $db->query($templine);
                $templine = '';
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/admin/backup')->with('error', 'Restore gagal. Database mungkin korup.');
        }

        return redirect()->to('/admin/backup')->with('success', 'Database berhasil direstore.');
    }

    // Code Backup (ZIP)
    public function codeExport()
    {
        $zipName = 'source_code_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = WRITEPATH . 'uploads/' . $zipName;

        if (!class_exists('ZipArchive')) {
            return redirect()->to('/admin/backup')->with('error', 'Ekstensi PHP ZipArchive tidak aktif. Silakan hubungi administrator server untuk mengaktifkan ekstensi "php_zip".');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->to('/admin/backup')->with('error', 'Tidak bisa membuat file ZIP.');
        }

        $rootPath = FCPATH . '../';  // Root project folder
        $realRoot = realpath($rootPath);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($realRoot),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($realRoot) + 1);

                // Exclude vendor, git, writable, node_modules
                if (
                    strpos($relativePath, 'vendor') === 0 ||
                    strpos($relativePath, '.git') === 0 ||
                    strpos($relativePath, 'writable') === 0 ||
                    strpos($relativePath, 'node_modules') === 0
                ) {
                    continue;
                }

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return $this->response->download($zipPath, null)->setFileName($zipName);
    }
}
