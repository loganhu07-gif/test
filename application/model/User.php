<?php
namespace app\model;

use function app\common\read_json;
use function app\common\write_json;

class User
{
    private string $storage = __DIR__ . '/../../storage/data/users.json';

    public function all(): array
    {
        return read_json($this->storage, []);
    }

    public function findByUsername(string $username): ?array
    {
        foreach ($this->all() as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }

    public function save(array $user): void
    {
        $users = $this->all();
        $existingIndex = null;
        foreach ($users as $index => $item) {
            if ($item['username'] === $user['username']) {
                $existingIndex = $index;
                break;
            }
        }
        if ($existingIndex !== null) {
            $users[$existingIndex] = $user;
        } else {
            $users[] = $user;
        }
        write_json($this->storage, $users);
    }
}
