<?php
namespace app\service;

use app\common\Database;

class BackupService
{
    private string $backupDir = __DIR__ . '/../../storage/backups';

    public function create(): string
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0775, true);
        }
        $stamp = date('Ymd_His');
        $target = $this->backupDir . "/backup_{$stamp}.json";
        $payload = [
            'users' => $this->fetch('users'),
            'test_results' => $this->fetch('test_results'),
            'treehole_posts' => $this->fetch('treehole_posts'),
            'captcha_tokens' => $this->fetch('captcha_tokens'),
        ];
        file_put_contents($target, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $target;
    }

    public function restore(string $filename): void
    {
        $path = $this->backupDir . '/' . basename($filename);
        if (!file_exists($path)) {
            throw new \RuntimeException('备份文件不存在');
        }
        $data = json_decode(file_get_contents($path), true);
        if (!$data) {
            throw new \RuntimeException('备份文件损坏');
        }
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $pdo->exec('DELETE FROM captcha_tokens');
            $pdo->exec('DELETE FROM treehole_posts');
            $pdo->exec('DELETE FROM test_results');
            $pdo->exec('DELETE FROM users');

            $this->bulkInsert($pdo, 'users', ['id','username','password','salt','role','created_at'], $data['users'] ?? []);
            $this->bulkInsert($pdo, 'test_results', ['id','answers','score','submitted_at'], $data['test_results'] ?? []);
            $this->bulkInsert($pdo, 'treehole_posts', ['id','content','tags','created_at'], $data['treehole_posts'] ?? []);
            $this->bulkInsert($pdo, 'captcha_tokens', ['token','answer','created_at'], $data['captcha_tokens'] ?? []);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw new \RuntimeException('恢复失败: ' . $e->getMessage());
        }
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

    private function fetch(string $table): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT * FROM {$table}");
        return $stmt->fetchAll();
    }

    private function bulkInsert(\PDO $pdo, string $table, array $columns, array $rows): void
    {
        if (empty($rows)) {
            return;
        }
        $colList = implode(',', $columns);
        $placeholders = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
        $stmt = $pdo->prepare("INSERT INTO {$table} ({$colList}) VALUES {$placeholders}");
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $col) {
                $values[] = $row[$col] ?? null;
            }
            $stmt->execute($values);
        }
    }
}
