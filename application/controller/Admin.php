<?php
namespace app\controller;

use app\service\AuthService;
use app\service\UploadService;
use function app\common\json_response;

class Admin
{
    private AuthService $auth;
    private UploadService $uploader;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->uploader = new UploadService();
    }

    public function dashboard(): void
    {
        $user = $this->currentUser();
        if (!$user) {
            json_response(['error' => '未登录'], 401);
            return;
        }
        json_response(['message' => '欢迎来到管理后台', 'user' => $user]);
    }

    public function upload(): void
    {
        $user = $this->currentUser();
        if (!$user || !$this->auth->hasPermission($user, 'manage_users')) {
            json_response(['error' => '无权限'], 403);
            return;
        }
        try {
            $result = $this->uploader->handle($_FILES['image'] ?? []);
            json_response(['message' => '上传成功', 'file' => $result]);
        } catch (\RuntimeException $e) {
            json_response(['error' => $e->getMessage()], 422);
        }
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
