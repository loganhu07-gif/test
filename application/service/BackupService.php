<?php
namespace app\service;

class BackupService
{
    private string $dataDir = __DIR__ . '/../../storage/data';
    private string $backupDir = __DIR__ . '/../../storage/backups';

    public function create(): string
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0775, true);
        }
        $stamp = date('Ymd_His');
        $target = $this->backupDir . "/backup_{$stamp}.zip";
        $zip = new \ZipArchive();
        if ($zip->open($target, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('无法创建备份');
        }
        foreach (scandir($this->dataDir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $zip->addFile($this->dataDir . '/' . $file, $file);
        }
        $zip->close();
        return $target;
    }

    public function restore(string $filename): void
    {
        $path = $this->backupDir . '/' . basename($filename);
        if (!file_exists($path)) {
            throw new \RuntimeException('备份文件不存在');
        }
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('无法打开备份文件');
        }
        $zip->extractTo($this->dataDir);
        $zip->close();
    }

    public function listBackups(): array
    {
        $items = [];
        foreach (scandir($this->backupDir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $items[] = $file;
        }
        sort($items);
        return $items;
    }
}
