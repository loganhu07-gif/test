<?php
namespace app\model;

use function app\common\read_json;
use function app\common\write_json;

class TreeholePost
{
    private string $storage = __DIR__ . '/../../storage/data/treehole_posts.json';

    public function all(): array
    {
        return read_json($this->storage, []);
    }

    public function add(array $post): void
    {
        $posts = $this->all();
        $posts[] = $post;
        write_json($this->storage, $posts);
    }
}
