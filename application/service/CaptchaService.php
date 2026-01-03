<?php
namespace app\service;

class CaptchaService
{
    private string $storage = __DIR__ . '/../../storage/data/captcha.json';

    public function generate(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $token = bin2hex(random_bytes(8));
        $answer = $a + $b;
        $payload = ['token' => $token, 'question' => "{$a} + {$b} = ?", 'answer' => $answer, 'created_at' => time()];
        file_put_contents($this->storage, json_encode($payload));
        return $payload;
    }

    public function verify(string $token, string $value): bool
    {
        if (!file_exists($this->storage)) {
            return false;
        }
        $saved = json_decode(file_get_contents($this->storage), true);
        if (!$saved || $saved['token'] !== $token) {
            return false;
        }
        $expired = (time() - ($saved['created_at'] ?? 0)) > 300;
        if ($expired) {
            return false;
        }
        return (int)$value === (int)$saved['answer'];
    }
}
