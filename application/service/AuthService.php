<?php
namespace app\service;

use app\model\User;

class AuthService
{
    private User $users;
    private array $config;

    public function __construct()
    {
        $this->users = new User();
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function createSalt(): string
    {
        return bin2hex(random_bytes((int)($this->config['salt_length'] / 2)));
    }

    public function hashPassword(string $password, string $salt): string
    {
        return hash('sha256', $salt . $password);
    }

    public function registerAdmin(string $username, string $password, string $role = 'editor'): array
    {
        $salt = $this->createSalt();
        $hash = $this->hashPassword($password, $salt);
        $user = [
            'username' => $username,
            'role' => $role,
            'salt' => $salt,
            'password' => $hash,
            'created_at' => date('c'),
        ];
        $this->users->save($user);
        return $user;
    }

    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->users->findByUsername($username);
        if (!$user) {
            return null;
        }
        $hash = $this->hashPassword($password, $user['salt']);
        if ($hash !== $user['password']) {
            return null;
        }
        return $user;
    }

    public function hasPermission(array $user, string $capability): bool
    {
        $role = $user['role'] ?? 'editor';
        $config = $this->config['admin_roles'][$role] ?? [];
        return $config[$capability] ?? false;
    }
}
