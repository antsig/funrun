<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
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
            $db = \Config\Database::connect();

            // Log activity
            (new ActivityLogModel())->log('backup_db_export');

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
        // 1. Disable in Production
        if (env('CI_ENVIRONMENT') === 'production') {  // Or strict 'production' check
            return redirect()->to('/admin/backup')->with('error', 'Fitur Restore dinonaktifkan di Production demi keamanan.');
        }

        // 2. Validation & Confirmation
        $validationRules = [
            'backup_file' => [
                'rules' => 'uploaded[backup_file]|max_size[backup_file,51200]|ext_in[backup_file,sql,txt]',
                'label' => 'File Backup'
            ],
            'confirm_restore' => [
                'rules' => 'required',
                'label' => 'Konfirmasi'
            ],
            'confirm_text' => [
                'rules' => 'required|in_list[RESTORE]',
                'label' => 'Ketik RESTORE',
                'errors' => [
                    'in_list' => 'Anda harus mengetik "RESTORE" untuk konfirmasi.'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->to('/admin/backup')->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('backup_file');

        // Additional MIME check if needed, though ext_in usually suffices
        $mime = $file->getMimeType();
        if (!in_array($mime, ['text/plain', 'application/octet-stream', 'application/sql', 'text/x-sql'])) {
            // return redirect()->to('/admin/backup')->with('error', 'Invalid file type: ' . $mime);
            // Skip strict mime check for now as sql mime types vary significantly
        }

        $sql = file_get_contents($file->getTempName());
        $db = \Config\Database::connect();

        $db->transStart();

        // Remove comments and execute
        $lines = explode("\n", $sql);
        $templine = '';

        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            $templine .= $line;

            if (substr(trim($line), -1, 1) == ';') {
                $db->query($templine);
                $templine = '';
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/admin/backup')->with('error', 'Restore gagal. Database mungkin korup.');
        }

        // Log Activity
        (new ActivityLogModel())->log('restore_db', null, 'Restored from file: ' . $file->getName());

        return redirect()->to('/admin/backup')->with('success', 'Database berhasil direstore.');
    }

    // Code Backup (ZIP)
    public function codeExport()
    {
        $zipName = 'source_code_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = WRITEPATH . 'uploads/' . $zipName;

        if (!class_exists('ZipArchive')) {
            return redirect()->to('/admin/backup')->with('error', 'Ekstensi PHP ZipArchive tidak aktif.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->to('/admin/backup')->with('error', 'Tidak bisa membuat file ZIP.');
        }

        (new ActivityLogModel())->log('backup_code_export');

        $rootPath = FCPATH . '../';  // Root project folder
        $realRoot = realpath($rootPath);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($realRoot),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($realRoot) + 1);

                // Normalizing slashes for Windows/Linux
                $relativePathNormalized = str_replace('\\', '/', $relativePath);

                // Exclude Rules
                if (
                    strpos($relativePathNormalized, 'vendor') === 0 ||
                    strpos($relativePathNormalized, '.git') === 0 ||
                    strpos($relativePathNormalized, 'node_modules') === 0 ||
                    strpos($relativePathNormalized, '.env') === 0 ||  // Exclude .env
                    strpos($relativePathNormalized, 'writable/logs') === 0 ||  // Exclude logs
                    strpos($relativePathNormalized, 'writable/cache') === 0 ||  // Exclude cache
                    strpos($relativePathNormalized, 'public/uploads/manifest') === 0  // Check specific sensitive folders?
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
