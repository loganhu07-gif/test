<?php
namespace app\controller;

use app\service\AuthService;
use app\service\BackupService;
use function app\common\json_response;

class Backup
{
    private BackupService $backup;
    private AuthService $auth;

    public function __construct()
    {
        $this->backup = new BackupService();
        $this->auth = new AuthService();
    }

    public function create(): void
    {
        if (!$this->isAuthorized()) {
            return;
        }
        try {
            $file = $this->backup->create();
            json_response(['message' => '备份成功', 'file' => basename($file)]);
        } catch (\RuntimeException $e) {
            json_response(['error' => $e->getMessage()], 500);
        }
    }

    public function restore(): void
    {
        if (!$this->isAuthorized()) {
            return;
        }
        $filename = $_POST['filename'] ?? '';
        try {
            $this->backup->restore($filename);
            json_response(['message' => '恢复成功']);
        } catch (\RuntimeException $e) {
            json_response(['error' => $e->getMessage()], 500);
        }
    }

    public function list(): void
    {
        if (!$this->isAuthorized()) {
            return;
        }
        json_response(['items' => $this->backup->listBackups()]);
    }

    private function isAuthorized(): bool
    {
        $user = $this->currentUser();
        if (!$user || !$this->auth->hasPermission($user, 'manage_backups')) {
            json_response(['error' => '无权限'], 403);
            return false;
        }
        return true;
    }

    private function currentUser(): ?array
    {
        $sessionFile = __DIR__ . '/../../storage/data/session.json';
        if (!file_exists($sessionFile)) {
            return null;
        }
        $session = json_decode(file_get_contents($sessionFile), true);
        return $session['user'] ?? null;
    }
}
