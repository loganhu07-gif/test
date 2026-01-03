<?php
namespace app\common;

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function read_json(string $path, array $default = []): array
{
    if (!file_exists($path)) {
        return $default;
    }
    $content = file_get_contents($path);
    if ($content === false || $content === '') {
        return $default;
    }
    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : $default;
}

function write_json(string $path, array $data): void
{
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function now(): string
{
    return date('Y-m-d H:i:s');
}
