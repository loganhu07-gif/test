<?php
namespace app\model;

use app\common\Database;
use PDO;

class User
{
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, username, role, salt, password, created_at FROM users ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function findByUsername(string $username): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, username, role, salt, password, created_at FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function save(array $user): void
    {
        $pdo = Database::connection();
        $exists = $this->findByUsername($user['username']);
        if ($exists) {
            $stmt = $pdo->prepare('UPDATE users SET password=:password, salt=:salt, role=:role WHERE username=:username');
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (username, password, salt, role, created_at) VALUES (:username, :password, :salt, :role, :created_at)');
        }
        $stmt->execute([
            'username' => $user['username'],
            'password' => $user['password'],
            'salt' => $user['salt'],
            'role' => $user['role'],
            'created_at' => $user['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
    }
}
