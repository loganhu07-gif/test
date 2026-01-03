<?php
namespace app\model;

use app\common\Database;

class TreeholePost
{
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, content, tags, created_at FROM treehole_posts ORDER BY id DESC');
        return array_map(function ($row) {
            $row['tags'] = $row['tags'] ? json_decode($row['tags'], true) : [];
            return $row;
        }, $stmt->fetchAll());
    }

    public function add(array $post): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO treehole_posts (content, tags, created_at) VALUES (:content, :tags, :created_at)');
        $stmt->execute([
            'content' => $post['content'],
            'tags' => json_encode($post['tags'], JSON_UNESCAPED_UNICODE),
            'created_at' => $post['created_at'],
        ]);
    }
}
