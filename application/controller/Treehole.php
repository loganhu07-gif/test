<?php
namespace app\controller;

use app\model\TreeholePost;
use function app\common\json_response;
use function app\common\now;

class Treehole
{
    private TreeholePost $posts;

    public function __construct()
    {
        $this->posts = new TreeholePost();
    }

    public function index(): void
    {
        json_response(['items' => $this->posts->all()]);
    }

    public function post(): void
    {
        $content = trim($_POST['content'] ?? '');
        $tags = $_POST['tags'] ?? [];
        if ($content === '') {
            json_response(['error' => '内容不能为空'], 422);
            return;
        }
        $payload = [
            'content' => $content,
            'tags' => is_array($tags) ? $tags : explode(',', (string)$tags),
            'created_at' => now(),
        ];
        $this->posts->add($payload);
        json_response(['message' => '发布成功', 'post' => $payload]);
    }
}
