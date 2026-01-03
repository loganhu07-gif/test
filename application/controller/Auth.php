<?php
namespace app\controller;

use app\service\AuthService;
use app\service\CaptchaService;
use function app\common\json_response;

class Auth
{
    private AuthService $auth;
    private CaptchaService $captcha;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->captcha = new CaptchaService();
    }

    public function captcha(): void
    {
        $payload = $this->captcha->generate();
        json_response(['token' => $payload['token'], 'question' => $payload['question']]);
    }

    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $captchaToken = $_POST['captcha_token'] ?? '';
        $captchaValue = $_POST['captcha_value'] ?? '';

        if (!$this->captcha->verify($captchaToken, $captchaValue)) {
            json_response(['error' => '验证码错误'], 422);
            return;
        }

        $user = $this->auth->authenticate($username, $password);
        if (!$user) {
            json_response(['error' => '用户名或密码错误'], 401);
            return;
        }

        $session = bin2hex(random_bytes(16));
        $payload = ['token' => $session, 'user' => ['username' => $user['username'], 'role' => $user['role']]];
        file_put_contents(__DIR__ . '/../../storage/data/session.json', json_encode($payload));
        json_response($payload);
    }
}
