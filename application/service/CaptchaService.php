<?php
namespace app\service;

use app\common\Database;

class CaptchaService
{
    public function generate(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $token = bin2hex(random_bytes(8));
        $answer = $a + $b;
        $payload = ['token' => $token, 'question' => "{$a} + {$b} = ?", 'answer' => $answer, 'created_at' => time()];

        $pdo = Database::connection();
        $stmt = $pdo->prepare('REPLACE INTO captcha_tokens (token, answer, created_at) VALUES (:token, :answer, :created_at)');
        $stmt->execute([
            'token' => $token,
            'answer' => $answer,
            'created_at' => $payload['created_at'],
        ]);
        return $payload;
    }

    public function verify(string $token, string $value): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT token, answer, created_at FROM captcha_tokens WHERE token = :token LIMIT 1');
        $stmt->execute(['token' => $token]);
        $saved = $stmt->fetch();
        if (!$saved) {
            return false;
        }
        $expired = (time() - (int)$saved['created_at']) > 300;
        if ($expired) {
            return false;
        }
        return (int)$value === (int)$saved['answer'];
    }
}
