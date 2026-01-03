<?php
namespace app\service;

class UploadService
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function handle(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('上传失败');
        }
        if ($file['size'] > $this->config['max_upload_size']) {
            throw new \RuntimeException('文件过大');
        }
        $type = mime_content_type($file['tmp_name']);
        if (!in_array($type, $this->config['upload_allowed_types'], true)) {
            throw new \RuntimeException('不支持的文件类型');
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('img_', true) . '.' . $ext;
        $target = __DIR__ . '/../../storage/uploads/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('保存失败');
        }
        return ['filename' => $name, 'path' => $target, 'type' => $type];
    }
}
